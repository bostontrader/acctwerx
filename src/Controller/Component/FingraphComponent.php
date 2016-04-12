<?php

namespace App\Controller\Component;

use Cake\Controller\Component;

//require_once(ROOT . DS . 'vendor/jpgraph/jpgraph/lib/JpGraph/src/jpgraph.php');
//require_once(ROOT . DS . 'vendor/jpgraph/jpgraph/lib/JpGraph/src/jpgraph_line.php');
//require_once(ROOT . DS . 'vendor/jpgraph/jpgraph/lib/JpGraph/src/jpgraph_utils.inc.php');

class FingraphComponent extends Component {

	public $graph;

	//private $xdata;
	//private $ydata;
	//private $graph_title;
	//private $goal;
	private $linePlots=[];
	//private $lplot;

	//private $graph_width   = 350;
	//private $graph_height  = 320;
	private $left_margin   = 60;
	private $stats_base_y  = 270;
	private $stats_line_ht = 12;
	
	//function __construct($xdata, $ydata, $graph_title="Graph Title", $goal=0) {
		//$this->xdata = $xdata;
		//$this->ydata = $ydata;
		//$this->graph_title = $graph_title;
		//$this->goal = $goal;
		//$this->graph = new \Graph($this->graph_width, $this->graph_height);
		//$this->linePlots = array();
	//}


	// Setup titles and X-axis labels
	//$this->Fingraph->graph->xaxis->title->Set('xaxis title');

	// Setup Y-axis title
	//$this->Fingraph->graph->yaxis->title->Set('yaxix title');
	// $p = precision of the mx part of the trend line
	//public function init($p=0) {
	public function init($title,$xdata,$ydata,$width=350,$height=320) {

		// 1. Setup some member variables
		//$graph_width=$width;
		//$graph_height=$height;
		//$graph_title=$title;


		// 2. We'll need a graph object
		$this->graph = new \Graph($width, $height);

		// 3. Set some random values
		$this->graph->SetScale("textlin");
		$this->graph->title->Set($title);

		// 4. Build the linear regression class
		$linreg = new \LinearRegression($xdata, $ydata);

		// 4.1 Get the basic linear regression statistics
		list( $stderr, $corr ) = $linreg->GetStat();
		list( $xd, $yd ) = $linreg->GetY(1,count($xdata));
		list( $b, $m)    = $linreg->GetAB();

		// 4.2 Create the regression line
		$lplot = new \LinePlot($yd);
		$lplot->SetWeight(3);
		$lplot->SetColor('navy');

		// 5. Calculate regression and goal statistics
		//$mp = CakeNumber::precision($m, 0);
		//$bp = CakeNumber::precision($b, 0);

		// 5.1 y=mx+b
		//$s = "y = ".CakeNumber::precision($m,$p)."x + $bp";
		$s = "y = $m x + $b";
		$txt = new \Text($s);
		$txt->SetPos($this->left_margin, $this->stats_base_y);
		// Set color and font for the text
		$txt->SetColor('red');
		$txt->SetFont(FF_FONT2,FS_BOLD);
		$this->graph->AddText($txt);

		// 4.2 present amount
		//$present_amt = end($this->ydata);
		//App::uses("CakeNumber", "Utility");
		//$txt = new Text("p=" . CakeNumber::precision($present_amt,0));
		//$txt->SetPos($this->left_margin, $this->stats_base_y+$this->stats_line_ht);
		//$this->graph->AddText($txt);

		// 4.3 the goal
		//$delta = $this->goal - $present_amt;
		//$deltaP = $delta/$m;
		//$deltaP = CakeNumber::precision($deltaP, 1);

		//$s = "g = $this->goal";
		//$txt = new Text($s);
		//$txt->SetPos($this->left_margin, $this->stats_base_y+$this->stats_line_ht*2);
		//$this->graph->AddText($txt);

		//$s = "t = $deltaP";
		//$txt = new Text($s);
		//$txt->SetPos($this->left_margin, $this->stats_base_y+$this->stats_line_ht*3);
		//$this->graph->AddText($txt);
		$this->graph->Add($lplot);
		//$this->graph->legend->SetPos(0.5,0.98,'center','bottom');
		//$this->graph->legend->SetAbsPos(0,0);
		//$this->graph->yaxis->SetColor('red');// this works here, but not earlier
		//$this->graph->xaxis->SetColor('blue');
		//$this->graph->SetAxisLabelBackground(1,'red');
		//$this->graph->SetAxisLabelBackground(2,'green');
		//$this->graph->SetBackgroundCFlag(2,BGIMG_FILLPLOT,100); // no discernable effect
		//$this->graph->SetBackgroundGradient('navy','silver',2,BGRAD_FRAME); // no discernable effect
		//$this->graph->SetBox(true, array(80,80,80), 10);
		//$this->graph->SetBox();
		//$this->graph->SetFrame(true,'darkblue',20);
		//$this->graph->SetFrameBevel(20,true,'black');
		//$this->graph->SetMarginColor('silver');
		//$this->graph->SetMargin($this->left_margin,50,50,50); // left, right, top, bottom
		//$this->graph->SetMargin(50,50,50,0); // left, right, top, bottom
	}

	public function addSeries($ydata, $legend, $color) {
		$lineplot = new \LinePlot($ydata);
		$lineplot->SetLegend($legend);
		$lineplot->SetFillColor($color);
		$lineplot->SetWeight(5);
		$this->linePlots[] = $lineplot;
		//return $lineplot;
	}

	public function buildPlot() {
		// Then add them together to form an accumulated plot
		//$ap = new AccLinePlot(array( $lineplotJewelry, $lineplotPapergold, $lineplotCoinsbullion ));
		$ap = new \AccLinePlot( $this->linePlots );
		$this->graph->Add($ap);
		//$this->graph->Add($this->lplot);
	}


}
