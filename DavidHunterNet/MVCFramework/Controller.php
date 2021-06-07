<?php

namespace DavidHunterNet\MVCFramework;

class Controller
{
    private $models_dir_path;
    private $views_dir_path;

    public function __construct( String $models_dir_path, String $views_dir_path )
    {
        if( substr( $models_dir_path, -1 ) != "/" )
            $models_dir_path .= "/";
        
        if( substr( $views_dir_path, -1 ) != "/" )
            $views_dir_path .= "/";
        
        if( ! is_dir( $models_dir_path ) )
            die( "Models Dir does not exist: " . $models_dir_path );
        
        if( ! is_dir( $views_dir_path ) )
            die( "Views Dir does not exist: " . $views_dir_path );
        
        $this->models_dir_path = $models_dir_path;
        $this->views_dir_path  = $views_dir_path;
    }

    public function model( String $name )
    {
        $file_ext = ".model.php";
        $file_path = $this->models_dir_path . $name . $file_ext;

        if( ! file_exists( $file_path ) )
            die( "Model does not exist: " . $name );
        
        require_once $file_path;

        if( ! class_exists( $name ) )
            die( "Model class does not exist: " . $name );
        
        $model_instance = new $name;

        if( ! $model_instance instanceof DavidHunterNet\MVCFramework\Model )
            die( "Invalid Model: " . $name );
        
        return $model_instance;
    }

    public function view( String $name, array $data = array() )
    {
        $file_ext = ".view.php";
        $file_path = $this->views_dir_path . $name . $file_ext;

        if( ! file_exists( $file_path ) )
            die( "View does not exist: " . $name );
        
        require_once $file_path;
    }

    public function getModelsDir()
    {
        return $this->models_dir_path;
    }

    public function getViewsDir()
    {
        return $this->views_dir_path;
    }
}