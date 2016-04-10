<?php
namespace App\Controller;
use Cake\Datasource\ConnectionManager;

require_once(ROOT . DS . 'vendor/jpgraph/jpgraph/lib/JpGraph/src/jpgraph.php');
require_once(ROOT . DS . 'vendor/jpgraph/jpgraph/lib/JpGraph/src/jpgraph_line.php');
require_once(ROOT . DS . 'vendor/jpgraph/jpgraph/lib/JpGraph/src/jpgraph_utils.inc.php');

class BooksController extends AppController {

    public $helpers = ['Fingraph','FinStat'];

    const BOOK_SAVED = "The book has been saved.";
    const BOOK_NOT_SAVED = "The book could not be saved. Please, try again.";
    const BOOK_DELETED = "The book has been deleted.";
    const CANNOT_DELETE_BOOK = "The book could not be deleted. Please, try again.";

    public function initialize() {
        parent::initialize();
        $this->loadComponent('RequestHandler');
    }

    public function add() {
        $this->request->allowMethod(['get','post']);
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
        $book = $this->Books->get($id);

        /* @var \Cake\Database\Connection $connection */
        $connection = ConnectionManager::get('default');
        $query="select
                categories.title as ct,
                accounts.title as at,
                currencies.symbol,
                sum(distributions.amount * distributions.drcr) as amount
            from distributions
            left join transactions on distributions.transaction_id=transactions.id
            left join books on transactions.book_id=books.id
            left join accounts on distributions.account_id=accounts.id
            left join categories on accounts.category_id=categories.id
            left join currencies on distributions.currency_id=currencies.id
            where books.id=$id
            and categories.id in (1,2,3)
            group by accounts.id, currencies.id
            order by categories.id";
        $lineItems=$connection->execute($query)->fetchAll('assoc');
        $this->set(compact('book','lineItems'));
        $this->set('_serialize', ['lineItems']); // makes JSON
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

    // display a graph of bank balances and short-term notes
    // feed this a comma delimited list of account numbers for each
    // category, pending a more robust solution
    public function bank($id = null){


        // Given a range of reporting months {y1, m1}, {y2,m2} inclusive...
        $start_period = $this->getStartPeriod();
        $stop_period  = $this->getStopPeriod();


        //$results = $this->FinStat->doSQL("where distributions.account_id in (13, 33, 35, 47, 49, 254, 275, 293) ");
        $results = $this->doSQL("where distributions.account_id in (13, 33, 35, 47, 49, 254, 275, 293) ");
        $ydataBank = $this->buildDatapoints($results, $start_period, $stop_period);


        //$results = $this->FinStat->doSQL("where distributions.account_id in (276,294) ");
        //$ydataNote = $this->FinStat->buildDatapoints($results, $start_period, $stop_period);

        // Now add the arrays together for the linear regression
        //$xdata = $this->FinStat->getXData();

        //$ydata = array();
        //for ($i = 0; $i < count($xdata); $i++) {
                //$n=0;
                //$n+=$ydataBank[$i];
                //$n+=$ydataNote[$i];
                //$ydata[] = $n;
        //}


        // Guess #2.
        // Width and height of the graph
        $width = 600; $height = 200;

        // Create a graph instance
        $graph = new \Graph($width,$height);
        //App::uses('Fingraph', 'Lib');
        //$theGraph = new \Fingraph($xdata, $ydata, "Bank/Notes", 100000);
        //$theGraph = new \App\View\Helper\FingraphHelper($xdata, $ydata, "Bank/Notes", 100000);
        //$theGraph->init();

        // Specify what scale we want to use,
        // int = integer scale for the X-axis
        // int = integer scale for the Y-axis
        $graph->SetScale('intint');

        // Setup a title for the graph
        $graph->title->Set('Sunspot example');

        // Setup titles and X-axis labels
        $graph->xaxis->title->Set('(year from 1701)');

        // Setup Y-axis title
        $graph->yaxis->title->Set('(# sunspots)');

        // Create the linear plot
        //$lineplot=new \LinePlot([1,2,8,16,32,64,128,256,512,1024,2048,4096,8192]);
        $lineplot=new \LinePlot($ydataBank);

        // Add the plot to the graph
        $graph->Add($lineplot);








//$lineplotBank = $theGraph->addSeries($ydataBank, "Bank", "green");
//$lineplotBank = $theGraph->addSeries($ydataNote, "Note", "red");


//$theGraph->buildPlot();

// Display the graph
//$theGraph->graph->Stroke();

        $this->response->type('image/png');
        $this->response->body($graph->Stroke());
        return $this->response;

        // Display the graph
        //$graph->Stroke();

        //$bal=$this->request->query['bal'];
        //$nal=$this->request->query['nal'];
        //$this->viewBuilder()
            //->layout('jpgraph_layout');
            //->helpers(['FinStat']);
        //$this->RequestHandler->respondAs("image/png");
        //$this->set(compact('bal','nal'));
        //$this->response->type('application/pdf');
        //$this->response->type(['p'=>'image/png']);
        $this->response->type('image/png');
        $this->response->body("catfood");
        //$this->render('graph_bank');
        return $this->response;
    }

    public function edit($id = null) {
        $this->request->allowMethod(['get', 'put']);
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
        $this->set('books', $this->Books->find());
    }

    public function view($id = null) {
        $this->request->allowMethod(['get']);
        $book = $this->Books->get($id);
        $this->set('book', $book);
    }

    private function getStartPeriod() {
        return ['year'=>2015, 'month'=>6];
    }

    private function getStopPeriod() {
        return ['year'=>2016, 'month'=>3];
    }

    // The x-labels on the graph...
    private function getXData() {
        //  6/2015 ----|
        // begin ----| |
        //           | |
        //           | |          3/2016  -|
        return array(1,2,3,4,5,6,7,8,9,10,11 /*12,13,14,15,16,17,18,19,20,21,22,23,24,25,26*/);
    }

    //public function buildDatapoints($results, $start_period, $stop_period) {
    private function buildDatapoints($results, $start_period, $stop_period) {

        //$ydata = array();
        $ydata=[];

        $dp_report_range = $start_period;
        //$dp_sql_result = array('year'=>$results[0][0]['year'], 'month'=>$results[0][0]['month']);
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
                //$dp_sql_result = array('year'=>$results[$p_sql_result][0]['year'], 'month'=>$results[$p_sql_result][0]['month']);
                $dp_sql_result = ['year'=>$results[$p_sql_result]['year'], 'month'=>$results[$p_sql_result]['month']];
            }
        }

        // Now save the $running_balance as the first ydata entry.  Maybe this is zero.
        $ydata[] = $running_balance;

        // now stroll through the report range
        // while p_sql <= p_report_range_stop {
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
                    //$dp_sql_result = array('year'=>$results[$p_sql_result][0]['year'], 'month'=>$results[$p_sql_result][0]['month']);
                    $dp_sql_result = ['year'=>$results[$p_sql_result]['year'], 'month'=>$results[$p_sql_result]['month']];
                }

            } else if( $this->periodOf($dp_report_range) < $this->periodOf($dp_sql_result)) {
                $ydata[] = $running_balance; // no activity in this period
                $dp_report_range = $this->nextPeriod($dp_report_range);
            } else { // dp_report_range must be > dp_sql_result
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
            " sum(distributions.amount) as sum from distributions " .
            " left join transactions on distributions.transaction_id = transactions.id " .
            " left join accounts_categories on accounts.id=accounts_categories.account_id ".
            " left join categories on accounts_categories.category_id=categories.id ".
            " where categories.title='Bank' ".
            " group by year, month" .
            " order by year, month" ;

        //$db = ConnectionManager::getDataSource('default');
        $db = ConnectionManager::get('default');
        $query = $db->query($sql);
        $n1=$query->execute();
        $n2=$query->fetchAll('assoc');
        return $n2;
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
