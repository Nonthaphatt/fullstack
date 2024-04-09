<?php
class Sql_connection
{

    public static function sql_Connection()
    {
        $cx =  mysqli_connect("localhost", "root", "", "shopping");

        return $cx;
    }

}
