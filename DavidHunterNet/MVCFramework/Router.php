<?php

namespace DavidHunterNet\MVCFramework;

class Router
{
    private $routes = array();
    private $errorRoutes = array();

    public function add( String $method, $host, String $path, array $callback )
    {
        if( substr( $path, 0, 1 ) != "/" )
            $path = "/" . $path;
        
        if( ! is_string( $host ) && ! is_array( $host ) )
            die( "Error Adding Route: Host must be either String or array!" );

        if( is_string( $host ) )
            $host = array( $host );

        foreach( $host as $h )
        {
            if( ! is_string( $h ) )
                die( "Error Adding Route - Hostname Not a String: " . $h );

            if( isset( $this->routes[ $method ][ $h ][ $path ] ) )
                die( "Error: A route already exists for method " . $method . " host " . $h . " and path " . $path . "!" );
            
            if( count( $callback ) < 2 || ! $callback[0] instanceof Controller )
                die( "Error: Specified callback array must have first element as valid Controller instance." );
            
            if( ! method_exists( $callback[0], $callback[1] ) )
                die( "Error: Callback method does not exist for specified Controller instance!" );
            
            $this->routes[ $method ][ $h ][ $path ] = $callback;
        }
    }

    public function addError( String $code, String $method, $host, String $path, array $callback )
    {
        if( substr( $path, 0, 1 ) != "/" )
            $path = "/" . $path;
        
        if( ! is_string( $host ) && ! is_array( $host ) )
            die( "Error Adding Route: Host must be either String or array!" );

        if( is_string( $host ) )
            $host = array( $host );

        foreach( $host as $h )
        {
            if( isset( $this->routesError[ $code ][ $method ][ $h ][ $path ] ) )
                die( "Error: A route already exists for code " . $code . " method " . $method . " host " . $h . " and path " . $path . "!" );
            
            if( count( $callback ) < 2 || ! $callback[0] instanceof Controller )
                die( "Error: Specified callback array must have first element as valid Controller instance." );
            
            if( ! method_exists( $callback[0], $callback[1] ) )
                die( "Error: Callback method does not exist for specified Controller instance!" );
            
            $this->routesError[ $code ][ $method ][ $h ][ $path ] = $callback;
        }
    }

    public function resolve( String $method, String $host, String $path )
    {
        if( substr( $path, 0, 1 ) != "/" )
            $path = "/" . $path;
        
        if( ! array_key_exists( $method, $this->routes ) || ! array_key_exists( $host, $this->routes[ $method ] ) || ! array_key_exists( $path, $this->routes[ $method ][ $host ] ) )
            return $this->resolveError( "notfound", $method, $host, $path );
        
        return $this->routes[ $method ][ $host ][ $path ];
    }

    public function resolveError( String $code, String $method, String $host, String $base_path )
    {
        if( strrpos( $base_path, "/" ) )
            $path = substr( $base_path, 0, strrpos( $base_path, "/" ) +1 );
        else
            $base_path = "/";

        if( ! array_key_exists( $code, $this->routesError ) || ! array_key_exists( $method, $this->routesError[ $code ] ) || ! array_key_exists( $host, $this->routesError[ $code ][ $method ] ) )
            return;
        
        $possibleRoutes = $this->routesError[ $code ][ $method ][ $host ];

        $path_parts = explode( "/", $base_path );

        for( $pp = count( $path_parts ); $pp >= -1; $pp-- )
        {
            $new_path = implode( "/", $path_parts );

            if( ! array_key_exists( $new_path, $possibleRoutes ) )
            {
                unset( $path_parts[ $pp ] );
                
                if( $pp != -1 )
                {
                    continue;
                }
                
                return;
            }

            $base_path = $new_path;
        }

        return $possibleRoutes[ $base_path ];
    }

    public function execute( String $method, String $host, String $path )
    {
        $route = $this->resolve( $method, $host, $path );
        
        if( is_callable( $route ) )
        {
            call_user_func( $route );
            return;
        }

        header("HTTP/1.0 404 Not Found");
        echo "Error 404: Not Found";
        return;
    }
}