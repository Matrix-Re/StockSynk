<?php

/**
 * Class Model
 *
 * This abstract class provides methods for database operations.
 */
abstract class Model
{

    /**
     * Database connection attribute.
     */
     public static $db = NULL;

    /**
     * Database connection details.
     */
     private static $AddressBDD = "127.0.0.1";
     private static $TypeServer = "mysql:host";
     private static $DBName = "stocksynk";
     private static $TypeBDD = "dbname";
     private static $Login = "root";
     private static $password = "";

    /**
     * Sets the database connection.
     *
     * It creates a new PDO instance and assigns it to the $db attribute.
     * If the connection fails, it displays an error message.
     */
     private static function SetBdd()
     {
          $bdd =  self::$TypeServer . "=" . self::$AddressBDD . ";" . self::$TypeBDD . "=" . self::$DBName;
          $user = self::$Login;
          $pass = self::$password;

          try {
               self::$db = new PDO($bdd, $user, $pass);
          } catch (Exception $th) {               
               die(Controller::Message("Erreur BDD", "Impossible de se connecter à la base de données<br>".$th->getMessage()));
          }
     }

    /**
     * Executes a query.
     *
     * It prepares and executes a query. If the query is a SELECT statement, it fetches all the results.
     * If the execution fails, it displays an error message.
     *
     * @param string $query The SQL query.
     * @param array|null $parameters The query parameters.
     * @return mixed The query result.
     */
     public static function ExecuteQuery($query, $parameters = NULL)
     {
          $resultat = NULL;
          if (self::$db == NULL) {
               self::SetBdd();
          }

          try {
               // Préparation est execution de la requête
               $req = self::$db->prepare($query);
               $resultat = $req->execute($parameters);

               if (strpos(strtolower($query), "select") === 0) {
                    try {
                         $resultat = $req->fetchAll();
                    } catch (\Throwable $th) {
                         die(Controller::Message("Erreur execution requête", "Impossible de récupérer le résultat de la requête<br>".$th."<br>".$query));
                    }
               }
          } catch (Throwable $th) {               
               die(Controller::Message("Erreur execution requête", "Impossible d'executer la requête<br>".$th."<br>".$query));
          }

          return $resultat;
     }

    /**
     * Gets the ID of the last inserted row.
     *
     * @return string The ID of the last inserted row.
     */
     public static function GetID()
     {
          return self::$db->lastInsertId();
     }
}
