<?php

//$b = App::import('Vendor', 'jpgraph/jpgraph');
//$b = App::import('Vendor', 'jpgraph/jpgraph_line');
//$b = App::import('Vendor', 'jpgraph/jpgraph_utils_inc');

require_once(ROOT . DS . 'vendor/jpgraph/jpgraph/lib/JpGraph/src/jpgraph.php');
require_once(ROOT . DS . 'vendor/jpgraph/jpgraph/lib/JpGraph/src/jpgraph_line.php');
require_once(ROOT . DS . 'vendor/jpgraph/jpgraph/lib/JpGraph/src/jpgraph_utils.inc.php');



// Given a range of reporting months {y1, m1}, {y2,m2} inclusive...
//$start_period = $this->FinStat->getStartPeriod();
//$stop_period  = $this->FinStat->getStopPeriod();


//$results = $this->FinStat->doSQL("where distributions.account_id in (13, 33, 35, 47, 49, 254, 275, 293) ");
//$results = $this->FinStat->doSQL("where distributions.account_id in (13, 33, 35, 47, 49, 254, 275, 293) ");
//$ydataBank = $this->FinStat->buildDatapoints($results, $start_period, $stop_period);

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

//App::uses('Fingraph', 'Lib');
//$theGraph = new \Fingraph($xdata, $ydata, "Bank/Notes", 100000);
//$theGraph = new \App\View\Helper\FingraphHelper($xdata, $ydata, "Bank/Notes", 100000);
//$theGraph->init();

//$lineplotBank = $theGraph->addSeries($ydataBank, "Bank", "green");
//$lineplotBank = $theGraph->addSeries($ydataNote, "Note", "red");


//$theGraph->buildPlot();

// Display the graph
//$theGraph->graph->Stroke();

?>