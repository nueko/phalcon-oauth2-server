<?php 

use Phalcon\Db\Column;
use Phalcon\Db\Index;
use Phalcon\Db\Reference;
use Phalcon\Mvc\Model\Migration;

/**
 * Class OauthSessionsMigration_200
 */
class OauthSessionsMigration_200 extends Migration
{
    /**
     * Define the table structure
     *
     * @return void
     */
    public function morph()
    {
        $this->morphTable('oauth_sessions', array(
                'columns' => array(
                    new Column(
                        'id',
                        array(
                            'type' => Column::TYPE_INTEGER,
                            'unsigned' => true,
                            'notNull' => true,
                            'autoIncrement' => true,
                            'size' => 10,
                            'first' => true
                        )
                    ),
                    new Column(
                        'client_id',
                        array(
                            'type' => Column::TYPE_VARCHAR,
                            'notNull' => true,
                            'size' => 40,
                            'after' => 'id'
                        )
                    ),
                    new Column(
                        'owner_type',
                        array(
                            'type' => Column::TYPE_VARCHAR,
                            'notNull' => true,
                            'size' => 40,
                            'after' => 'client_id'
                        )
                    ),
                    new Column(
                        'owner_id',
                        array(
                            'type' => Column::TYPE_VARCHAR,
                            'notNull' => true,
                            'size' => 255,
                            'after' => 'owner_type'
                        )
                    ),
                    new Column(
                        'client_redirect_uri',
                        array(
                            'type' => Column::TYPE_VARCHAR,
                            'size' => 255,
                            'after' => 'owner_id'
                        )
                    ),
                    new Column(
                        'created_at',
                        array(
                            'type' => Column::TYPE_INTEGER,
                            'unsigned' => true,
                            'size' => 11,
                            'after' => 'client_redirect_uri'
                        )
                    ),
                    new Column(
                        'updated_at',
                        array(
                            'type' => Column::TYPE_INTEGER,
                            'unsigned' => true,
                            'size' => 11,
                            'after' => 'created_at'
                        )
                    )
                ),
                'indexes' => array(
                    new Index('PRIMARY', array('id'), null),
                    new Index('oauth_sessions_client_id_owner_type_owner_id_index', array('client_id', 'owner_type', 'owner_id'), null)
                ),
                'references' => array(
                    new Reference(
                        'oauth_sessions_client_id_foreign',
                        array(
                            'referencedSchema' => 'oauth2',
                            'referencedTable' => 'oauth_clients',
                            'columns' => array('client_id'),
                            'referencedColumns' => array('id')
                        )
                    )
                ),
                'options' => array(
                    'TABLE_TYPE' => 'BASE TABLE',
                    'AUTO_INCREMENT' => '1',
                    'ENGINE' => 'InnoDB',
                    'TABLE_COLLATION' => 'utf8_unicode_ci'
                ),
            )
        );
    }

    /**
     * Run the migrations
     *
     * @return void
     */
    public function up()
    {

    }

    /**
     * Reverse the migrations
     *
     * @return void
     */
    public function down()
    {

    }

}
