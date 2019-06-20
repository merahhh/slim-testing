<?php

use Phinx\Migration\AbstractMigration;

class GuestbookPhinx extends AbstractMigration
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
        $this->execute("CREATE TABLE IF NOT EXISTS slim_phinx_guestbook (
            id BIGINT(11) NOT NULL AUTO_INCREMENT,
            name VARCHAR(250) NOT NULL,
            email VARCHAR(250) NOT NULL,
            message VARCHAR(250) NOT NULL,
            image LONGTEXT NULL,
            type VARCHAR(250) NULL,
            time VARCHAR(50) NOT NULL,
            date VARCHAR(50) NOT NULL,
            PRIMARY KEY (id)
        )");

        $datas = $this->fetchAll("SELECT * FROM guestbook.guestbook");
        foreach ($datas as $data){
            $id = $data['id'];
            $name = $data['name'];
            $email = $data['email'];
            $message = $data['message'];
            $image = $data['image'];
            $type = $data['type'];
            $time = $data['time'];
            $date = $data['date'];

            //$this->table('slim_phinx_users')->insert($data)->update();
            $this->execute("INSERT INTO slim_phinx_guestbook (id, name, email, message,
                        image, type, time, date) VALUES ('$id', '$name', '$email', '$message',
                        '$image', '$type', '$time', '$date')");
        }
    }

    public function down(){
        $this->execute("DROP TABLE IF EXISTS guestbook;
            CREATE TABLE IF NOT EXISTS guestbook (
            id BIGINT(11) NOT NULL AUTO_INCREMENT,
            name VARCHAR(250) NOT NULL,
            email VARCHAR(250) NOT NULL,
            message VARCHAR(250) NOT NULL,
            image LONGTEXT NULL,
            type VARCHAR(250) NULL,
            time VARCHAR(50) NOT NULL,
            date VARCHAR(50) NOT NULL,
            PRIMARY KEY (id)
        )");

        $datas = $this->fetchAll("SELECT * FROM slim_phinx_guestbook");
        foreach ($datas as $data){
            $id = $data['id'];
            $name = $data['name'];
            $email = $data['email'];
            $message = $data['message'];
            $image = $data['image'];
            $type = $data['type'];
            $time = $data['time'];
            $date = $data['date'];

            $this->execute("INSERT INTO guestbook (id, name, email, message,
                        image, type, time, date) VALUES ('$id', '$name', '$email', '$message',
                        '$image', '$type', '$time', '$date')");
        }
    }
}
