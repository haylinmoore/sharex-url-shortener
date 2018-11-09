<?php
require 'flight/Flight.php';
Flight::set('key', "jhsdkfjahsdm");
Flight::set('folder', "u/");

Flight::route('POST /upload', function(){
    if ($_POST["key"] == Flight::get('key')){
            $fol = Flight::get('folder');
          $data = array();
          $data["filename"] = $_FILES["fdata"]['name'];
          $data["buffer"] = $_FILES["fdata"]["tmp_name"];
          $data["extension"] = pathinfo($_FILES["fdata"]['name'], PATHINFO_EXTENSION);
          $data["final-save-name"] = $fol . $data["filename"] . "." . $data["extension"];
          
          if(move_uploaded_file($data["buffer"], $data["final-save-name"]))
          {
            $file_signed = substr(base_convert(md5($data["final-save-name"]), 16,32), 0, 12); // Sign file with a crc32 and md5'd file hash (Also good because they cant upload the same file twice)
            rename($data["final-save-name"], $fol . $file_signed . "." . $data["extension"]); // Rename file
            
            $returned = array();
            $returned['filename'] = $fol . $file_signed . "." . $data["extension"];
            $returned['deletion'] = "del/" . $file_signed . "." . $data["extension"] . "/" . Flight::get('key');
            
            die(json_encode($returned));
          }
          else
          {
            die("FILE_CANT_UPLOAD");
          }
          die("FILE_ERROR_UNKNOWN");
    }
});


Flight::route('GET /del/@name/@key', function($name, $key){
    if ($key == Flight::get('key')){
        unlink(Flight::get('folder') . $name);
          
    }
});

Flight::start();