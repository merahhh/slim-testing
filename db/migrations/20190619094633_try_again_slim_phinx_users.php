<?php

use Phinx\Migration\AbstractMigration;

class TryAgainSlimPhinxUsers extends AbstractMigration
{
    /**
     * Change Method.
     *
     * Write your reversible migrations using this method.
     *
     * More information on writing migrations is available here:
     * http://docs.phinx.org/en/latest/migrations.html#the-abstractmigration-class
     *
     * The following commands can be used in this method and Phinx will
     * automatically reverse them when rolling back:
     *
     *    createTable
     *    renameTable
     *    addColumn
     *    addCustomColumn
     *    renameColumn
     *    addIndex
     *    addForeignKey
     *
     * Any other destructive changes will result in an error when trying to
     * rollback the migration.
     *
     * Remember to call "create()" or "update()" and NOT "save()" when working
     * with the Table class.
     */
    public function up()
    {
        $this->execute("CREATE TABLE IF NOT EXISTS slim_phinx_users (
            id INT NOT NULL AUTO_INCREMENT,
            first_name VARCHAR(50) NOT NULL,
            last_name VARCHAR(50) NOT NULL,
            email VARCHAR(100) NOT NULL,
            password VARCHAR(100) NOT NULL,
            hash VARCHAR(100) NOT NULL,
            active TINYINT(1) NULL DEFAULT 0,
            PRIMARY KEY (id)
        )");

//        $stmt = $this->query('SELECT * FROM users'); // returns PDOStatement
//        $data = $stmt->fetchAll(); // returns the result as an array
//        $data = [
//            'first_name'  => 'phinx',
//            'last_name' => 'merah',
//            'email' => 'a2@yahoo.com',
//            'password' => '123',
//            'hash' => 'mmmmmmmm',
//        ];

        $datas = $this->fetchAll("SELECT * FROM guestbook.users");
        foreach ($datas as $data){
            $id = $data['id'];
            $first_name = $data['first_name'];
            $last_name = $data['last_name'];
            $email = $data['email'];
            $password = $data['password'];
            $hash = $data['hash'];
            $active = $data['active'];

            //$this->table('slim_phinx_users')->insert($data)->update();
            $this->execute("INSERT INTO slim_phinx_users (id, first_name, last_name, email,
                        password, hash, active) VALUES ('$id', '$first_name', '$last_name', '$email',
                        '$password', '$hash', '$active')");
        }
    }

    public function down(){
        $this->execute("DROP TABLE IF EXISTS users;
            CREATE TABLE IF NOT EXISTS users (
            id INT NOT NULL AUTO_INCREMENT,
            first_name VARCHAR(50) NOT NULL,
            last_name VARCHAR(50) NOT NULL,
            email VARCHAR(100) NOT NULL,
            password VARCHAR(100) NOT NULL,
            hash VARCHAR(100) NOT NULL,
            active TINYINT(1) NULL DEFAULT 0,
            PRIMARY KEY (id)
        )");

        $datas = $this->fetchAll("SELECT * FROM slim_phinx_users");
        foreach ($datas as $data){
            $id = $data['id'];
            $first_name = $data['first_name'];
            $last_name = $data['last_name'];
            $email = $data['email'];
            $password = $data['password'];
            $hash = $data['hash'];
            $active = $data['active'];

            $this->execute("INSERT INTO users (id, first_name, last_name, email,
                        password, hash, active) VALUES ('$id', '$first_name', '$last_name', '$email',
                        '$password', '$hash', '$active')");
        }
    }
}
