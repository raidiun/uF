<?php
	//$inputFileStr = file_get_contents($_GET['url']);
	$inputFileStr = file_get_contents("example.html");
	
    function parseUF($fromString) {
        $pattern = "/\<(\w+)\s*[^\>\<]*data-uf=[\"']([^\"']*)[\"'][^\>\<]*>/";//Initial Pattern
        
        $output = array();
        $building = 0;
        $output[$building] = array();
        
        $path;
        $tagToClose;
        $thisMatch;
        $offset = 0;
        $tree = array();
        $lastMatchClosed = false;
        
        while(preg_match($pattern,$fromString,$thisMatch,PREG_OFFSET_CAPTURE,$offset)) {
            if($thisMatch[1][0] != "") {
                $tagToClose = $thisMatch[1][0];
                $offset = $thisMatch[0][1] + 4;
                $pattern = '/\<(\w+)\s*[^\>\<]*data-uf=["\']([^"\']*)["\'][^\>\<]*>|\<\/('.$tagToClose.')>/';
                $path = buildPath($thisMatch[2][0],$tree);
                $tree = addToTree($tree,$tagToClose,$path);
                $lastMatchClosed = false;
                }
            else {
                if(!$lastMatchClosed) {
                    $data = getData($fromString,($offset - 4),$tagToClose);
                    $output[$building] = buildArrayTree($data,end($tree)[1],$output[$building]);
                    //Tag that is being closed was just opened
                    //Need to track back for data and add it to the output JSON set
                    }
                $offset = $thisMatch[0][1] + 4;
                $tree = removeFromTree($tree,$thisMatch[3][0]);
                if(count($tree) == 0) {
                    $building = $building + 1;
                    $output[$building] = array();
                    }
                $lastMatchClosed = true;
                }
            //print_r($thisMatch);
            }
        return(json_encode($output));
        }
    
    function addToTree($tree,$tag,$path) {
        array_push($tree,array($tag,$path));
        return($tree);
        }
    
    function removeFromTree($tree,$tag) {
        if(end($tree)[0] == $tag) {
            array_pop($tree);
            }
        else {
            trigger_error('Mismatched tag close: '.$tag.' != '.end($tree)[0]);
            }
        return($tree);
        }
    
    function getData($string,$offset,$tag) {
        $match;
        $pattern = '<'.$tag.'[^\>]*\>([^\<]*)\</'.$tag.'\w*>';
        preg_match($pattern,$string,$match,0,$offset);
        return($match[1]);
        }
    
    function buildPath($path,$tree) {
        $pathArr = preg_split('/:/',$path);
        if($pathArr[0] == '') {
            unset($pathArr[0]);
            $pathArr = array_merge(end($tree)[1],$pathArr);
            }
        return($pathArr);
        }
    
    function buildArrayTree($data,$path,$object) {
        if($object['uFType'] == NULL) {//Object type not set
            $object['uFType'] = $path[0];
            }
        elseif ($object['uFType'] != $path[0]) {//Object type mismatch
            trigger_error('Object type ('.$object['uFType'].') does not match path root:'.$path[0]);
            return($object);
            }
        unset($path[0]);
        $ref =& $object;
        foreach($path as $pathFragment) {
            $tref =& $ref;
            unset($ref);
            $ref =& $tref[$pathFragment];
            unset($tref);
            if(($ref == NULL) && ($pathFragment != end($path))) {
                $ref = array();
                }
            }
        $ref = $data;
        return($object);
        }
    
    echo parseUF($inputFileStr);
    echo $_GET['url'];
	?>