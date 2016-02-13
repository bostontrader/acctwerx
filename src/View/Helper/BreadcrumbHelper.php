<?php

namespace App\View\Helper;
use Cake\View\Helper;

class BreadcrumbHelper extends Helper {

    public function makeTrail($label) {
        // Read the present trail or init if none.
        $this->request->session()->delete('catfood');
        $sessionCrumbs=$this->request->session()->read('catfood');
        if(is_null($sessionCrumbs))$sessionCrumbs=[];
        //is_null($sessionCrumbs)?$sessionCrumbs=[]:$sessionCrumbs=unserialize($sessionCrumbs);

        // Get the present url and parse into a parameter array
        $requestUrl=$this->request->url;
        $n2=\Cake\Routing\Router::parse($requestUrl);

        // Is the present url present in the present trail?
        foreach($sessionCrumbs as $crumb) {
            // does this crumb match the present url?
            // If so, snip everything from here to the end
        }
        $sessionCrumbs[$requestUrl]=['label'=>$label,'params'=>$n2];


        // now append the present url to the trail

        // save the trail to the session
        $n3=serialize($n2);
        $sessionCrumbs=$this->request->session()->write('catfood',$sessionCrumbs);

        // Now add the crumbs to ordinary way
        foreach($sessionCrumbs as $crumb){
            $this->Html->addCrumb('Books', '/books');
        }
    }
}
