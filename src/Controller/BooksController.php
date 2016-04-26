<?php
namespace App\Controller;

use Cake\Datasource\ConnectionManager;
use Cake\Network\Exception\BadRequestException;

require_once(ROOT . DS . 'vendor/jpgraph/jpgraph/lib/JpGraph/src/jpgraph.php');
require_once(ROOT . DS . 'vendor/jpgraph/jpgraph/lib/JpGraph/src/jpgraph_line.php');
require_once(ROOT . DS . 'vendor/jpgraph/jpgraph/lib/JpGraph/src/jpgraph_utils.inc.php');

class BooksController extends AppController {

    public $helpers = ['Fingraph','FinStat'];

    const BOOK_SAVED = "The book has been saved.";
    const BOOK_NOT_SAVED = "The book could not be saved. Please, try again.";
    const BOOK_DELETED = "The book has been deleted.";
    const CANNOT_DELETE_BOOK = "The book could not be deleted. Please, try again.";

    //public function initialize() {
        //parent::initialize();
        //$this->loadComponent('RequestHandler');
    //}

    // GET | POST /books/add
    public function add() {
        $this->request->allowMethod(['get','post']);

        // Neither GET nor POST should accept any query string params.
        if(count($this->request->query)>0)
            throw new BadRequestException(self::THAT_QUERY_PARAMETER_NOT_ALLOWED);

        $book = $this->Books->newEntity();
        if ($this->request->is('post')) {
            $book = $this->Books->patchEntity($book, $this->request->data);
            if ($this->Books->save($book)) {
                $this->Flash->success(__(self::BOOK_SAVED));
                return $this->redirect(['controller'=>'books','action' => 'index','_method'=>'GET']);
            } else {
                $this->Flash->error(__(self::BOOK_NOT_SAVED));
            }
        }
        $this->set(compact('book'));
        return null;
    }

    public function balance($id = null) {
        $this->request->allowMethod(['get']);

        // Should not accept any query string params.
        if(count($this->request->query)>0)
            throw new BadRequestException(self::THAT_QUERY_PARAMETER_NOT_ALLOWED);

        $book = $this->Books->get($id);




        /* @var \Cake\Database\Connection $connection */
        $connection = ConnectionManager::get('default');

        $query=$this->buildBalanceSheetQuery('A,C',$id);
        $lineItemsCash=$connection->execute($query)->fetchAll('assoc');
        $this->set(compact('book','lineItemsCash'));

        $query=$this->buildBalanceSheetQuery('A,B',$id);
        $lineItemsBank=$connection->execute($query)->fetchAll('assoc');
        $this->set(compact('book','lineItemsBank'));
        //$this->set('_serialize', ['lineItems']); // makes JSON
    }

    // Given a string to match concatenated category symbols, build the
    // query string for element of the balance sheet
    private function buildBalanceSheetQuery($ct,$book_id) {
        // 1. Get a list of all accounts, from book_id, along with the denormalized
        // list of categories said account is tagged with.
        // | id | ct    |
        // |  1 | A,B   |
        // |  2 | A,C,D |
        $q1 = "select accounts.id, group_concat(categories.symbol order by categories.symbol) as ct
    from accounts 
    left join accounts_categories on accounts.id = accounts_categories.account_id
    left join categories on accounts_categories.category_id = categories.id
	where accounts.book_id = $book_id
	group by accounts.id";

        // 2. Prune this list to include only the account_ids of accounts, with a very
        // specific concatenated list of category.symbols.
        // | id |
        // |  1 |
        // |  2 |
        //$ct='A,C';
        $q2="select id from ($q1) as t2 where ct = '$ct'";

        // 3. Now find all distributions for these accounts.
        // | at     | symbol | amount   |
        // | Bank 1 | RMB    |   100.00 |
        // | Bank 2 | RUB    |   250.00 |
        $q3="select
    accounts.title as at,
    currencies.symbol,
    sum(distributions.amount * distributions.drcr) as amount
    from distributions 
    left join accounts on distributions.account_id=accounts.id
    left join currencies on distributions.currency_id=currencies.id 
    where account_id in ($q2)
    group by accounts.id, currencies.id";

        return $q3;
    }

    //public function delete($id = null) {
        //$this->request->allowMethod(['post', 'delete']);
        //$book = $this->Books->get($id);
        //if ($this->Books->delete($book)) {
            //$this->Flash->success(__(self::BOOK_DELETED));
        //} else {
            //$this->Flash->error(__(self::CANNOT_DELETE_BOOK));
        //}
        //return $this->redirect(['action' => 'index']);
    //}

    // display a graph of bank balances + notes
    public function graphBank($id = null){
        $this->request->allowMethod(['get']);

        // Should not accept any query string params.
        if(count($this->request->query)>0)
            throw new BadRequestException(self::THAT_QUERY_PARAMETER_NOT_ALLOWED);

        // 1. Start with a range of reporting months {y1, m1}, {y2,m2} inclusive...
        $start_period = $this->getStartPeriod();
        $stop_period  = $this->getStopPeriod();

        // 2. Query the db and hammer the results into the proper format.
        $results = $this->doSQL("where categories.symbol in ('B') ");
        $ydataBank = $this->buildDatapoints($results, $start_period, $stop_period);

        $results = $this->doSQL("where categories.symbol in ('STN') ");
        $ydataNote = $this->buildDatapoints($results, $start_period, $stop_period);

        // 3. Now add the arrays together in order to build a linear regression
        // trendline on their sum. The arrays should have an equal number of elements.
        $xdata = $this->getXData();
        $ydata = [];
        for ($i = 0; $i < count($xdata); $i++) {
            $n=0;
            $n+=$ydataBank[$i];
            $n+=$ydataNote[$i];
            $ydata[] = $n;
        }

        // 4. The graph is implemented as a component.  Now set that up.
        $this->loadComponent('Fingraph');
        $this->Fingraph->init("Bank/Notes",$xdata,$ydata);


        $this->Fingraph->addSeries($ydataBank, "Bank", "green");
        $this->Fingraph->addSeries($ydataNote, "Notes", "red");


        $this->Fingraph->buildPlot();

        $this->response->type('image/png');
        $this->response->body($this->Fingraph->graph->stroke());
        return $this->response;
    }

    // display a graph of cash balances
    public function graphCash($id = null){

        $this->request->allowMethod(['get']);

        // Should not accept any query string params.
        if(count($this->request->query)>0)
            throw new BadRequestException(self::THAT_QUERY_PARAMETER_NOT_ALLOWED);

        // 1. Start with a range of reporting months {y1, m1}, {y2,m2} inclusive...
        $start_period = $this->getStartPeriod();
        $stop_period  = $this->getStopPeriod();

        // 2. Query the db and hammer the results into the proper format.
        $results = $this->doSQL("where categories.symbol in ('C') ");
        $ydataCash = $this->buildDatapoints($results, $start_period, $stop_period);

        //$results = $this->doSQL("where categories.symbol in ('STN') ");
        //$ydataNote = $this->buildDatapoints($results, $start_period, $stop_period);

        // 3. Now add the arrays together in order to build a linear regression
        // trendline on their sum. The arrays should have an equal number of elements.
        $xdata = $this->getXData();
        $ydata = [];
        for ($i = 0; $i < count($xdata); $i++) {
            $n=0;
            $n+=$ydataCash[$i];
            //$n+=$ydataNote[$i];
            $ydata[] = $n;
        }

        // 4. The graph is implemented as a component.  Now set that up.
        $this->loadComponent('Fingraph');
        $this->Fingraph->init("Cash",$xdata,$ydata);

        $this->Fingraph->addSeries($ydataCash, "Cash", "green");

        $this->Fingraph->buildPlot();

        $this->response->type('image/png');
        $this->response->body($this->Fingraph->graph->stroke());
        return $this->response;
    }


    public function edit($id = null) {
        $this->request->allowMethod(['get', 'put']);

        // Neither GET nor PUT should accept any query string params.
        if(count($this->request->query)>0)
            throw new BadRequestException(self::THAT_QUERY_PARAMETER_NOT_ALLOWED);

        $book = $this->Books->get($id);
        if ($this->request->is(['put'])) {
            $book = $this->Books->patchEntity($book, $this->request->data);
            if ($this->Books->save($book)) {
                $this->Flash->success(__(self::BOOK_SAVED));
                return $this->redirect(['controller'=>'books','action' => 'index','_method'=>'GET']);
            } else {
                $this->Flash->error(__(self::BOOK_NOT_SAVED));
            }
        }
        $this->set(compact('book'));
        return null;
    }

    public function income($id = null) {
        $this->request->allowMethod(['get']);

        // Should not accept any query string params.
        if(count($this->request->query)>0)
            throw new BadRequestException(self::THAT_QUERY_PARAMETER_NOT_ALLOWED);

        $book = $this->Books->get($id);

        /* @var \Cake\Database\Connection $connection */
        $connection = ConnectionManager::get('default');
        $query="select categories.title as ct, accounts.title as at, sum(distributions.amount * distributions.drcr) as amount
            from distributions
            left join transactions on distributions.transaction_id=transactions.id
            left join books on transactions.book_id=books.id
            left join accounts on distributions.account_id=accounts.id
            left join categories on accounts.category_id=categories.id
            where books.id=$id
            and categories.id in (4,5)
            group by accounts.id
            order by categories.id";
        $lineItems=$connection->execute($query)->fetchAll('assoc');

        $this->set(compact('book','lineItems' ));
    }

    public function index() {
        $this->request->allowMethod(['get']);

        // Should not accept any query string params.
        if(count($this->request->query)>0)
            throw new BadRequestException(self::THAT_QUERY_PARAMETER_NOT_ALLOWED);

        $this->set('books', $this->Books->find());
    }

    public function view($id = null) {
        $this->request->allowMethod(['get']);

        // Should not accept any query string params.
        if(count($this->request->query)>0)
            throw new BadRequestException(self::THAT_QUERY_PARAMETER_NOT_ALLOWED);

        $book = $this->Books->get($id);
        $this->set('book', $book);
    }

    // This is really the 2nd point displayed on the graph.
    private function getStartPeriod() {
        return ['year'=>2016, 'month'=>1];
    }

    private function getStopPeriod() {
        return ['year'=>2016, 'month'=>4];
    }

    // The x-labels on the graph...
    private function getXData() {
        //  1/2016 ----|
        // begin ----| |
        //           | |
        //           | |       4/2016  -|
        return array(1,2,3,4,           5);
    }

    private function buildDatapoints($results, $start_period, $stop_period) {

        $ydata=[];

        $dp_report_range = $start_period;
        $dp_sql_result = ['year'=>$results[0]['year'], 'month'=>$results[0]['month']];
        $p_sql_result = 0;

        // compute beginning balance, if any
        $running_balance = 0;
        while( $this->periodOf($dp_sql_result) < $this->periodOf($start_period) ) {
            $running_balance+= $results[$p_sql_result]['sum'];

            // Now advance to the next sql entry.  If already at the end,
            // Then set crazy high period to indicate
            // Duplicate code!
            $rowcnt = count($results);
            if ($p_sql_result+1 >= $rowcnt) {
                // Already at the end
                $dp_sql_result = ['year'=>2099, 'month'=>12];
            }	else {
                // Not at the end. Advance to next row.
                $p_sql_result++;
                $dp_sql_result = ['year'=>$results[$p_sql_result]['year'], 'month'=>$results[$p_sql_result]['month']];
            }
        }

        // Now save the $running_balance as the first ydata entry.  Maybe this is zero.
        $ydata[] = $running_balance;

        // now stroll through the report range
        while( $this->periodOf($dp_report_range) <= $this->periodOf($stop_period)) {
            if( $this->periodOf($dp_report_range) == $this->periodOf($dp_sql_result) ) {
                $running_balance+= $results[$p_sql_result]['sum'];
                $ydata[] = $running_balance;
                $dp_report_range = $this->nextPeriod($dp_report_range);

                // Now advance to the next sql entry.  If already at the end,
                // Then set crazy high period to indicate
                // Duplicate code!
                $rowcnt = count($results);
                if ($p_sql_result+1 >= $rowcnt) {
                    // Already at the end
                    $dp_sql_result = ['year'=>2099, 'month'=>12];
                }	else {
                    // Not at the end. Advance to next row.
                    $p_sql_result++;
                    $dp_sql_result = ['year'=>$results[$p_sql_result]['year'], 'month'=>$results[$p_sql_result]['month']];
                }

            } else if( $this->periodOf($dp_report_range) < $this->periodOf($dp_sql_result)) {
                $ydata[] = $running_balance; // no activity in this period
                $dp_report_range = $this->nextPeriod($dp_report_range);
            } else {
                // dp_report_range must be > dp_sql_result
                // This should be an error.  $dp_report_range should never
                // be able to outrun $dp_sql_result
                $i = 5/0;
            }
        }
        return $ydata;
    }

    //function startAtZeroForExpenses($ydata) {
    //$n = $ydata[0];
    //for ($i = 0; $i < count($ydata); $i++) {
    //$ydata[$i] -= $n;
    //}
    //return $ydata;
    //}

    // example output
    // | year | month | sum |
    // | 2011 | 05    | 100.00 |
    // | 2011 | 06    | 200.00 |
    private function doSQL($where_clause) {
        $sql = "select " .
            " DATE_FORMAT(transactions.tran_datetime, '%Y') as year, " .
            " DATE_FORMAT(transactions.tran_datetime, '%m') as month, " .
            " sum(distributions.amount * distributions.drcr) as sum from distributions " .

            " left join transactions on distributions.transaction_id = transactions.id " .
            " left join accounts on distributions.account_id = accounts.id " .
            " left join accounts_categories on accounts.id = accounts_categories.account_id " .
            " left join categories on accounts_categories.category_id = categories.id " .

            " $where_clause ".
            " group by year, month" .
            " order by year, month" ;

        $db = ConnectionManager::get('default');
        $query = $db->query($sql);
        $query->execute();
        return $query->fetchAll('assoc');
    }

    private function periodOf($period) {
        return $period['year'] * 12 + $period['month'];
    }

    private function nextPeriod($period) {
        $year  = $period['year'];
        $month = $period['month'];
        $month++;
        if ($month > 12) {
            $month = 1;
            $year++;
        }
        $period = array('year'=>$year, 'month'=>$month);
        return $period;
    }


}
