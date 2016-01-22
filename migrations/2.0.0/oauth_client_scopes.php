<?php 

use Phalcon\Db\Column;
use Phalcon\Db\Index;
use Phalcon\Db\Reference;
use Phalcon\Mvc\Model\Migration;

/**
 * Class OauthClientScopesMigration_200
 */
class OauthClientScopesMigration_200 extends Migration
{
    /**
     * Define the table structure
     *
     * @return void
     */
    public function morph()
    {
        $this->morphTable('oauth_client_scopes', array(
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
                        'scope_id',
                        array(
                            'type' => Column::TYPE_VARCHAR,
                            'notNull' => true,
                            'size' => 40,
                            'after' => 'client_id'
                        )
                    ),
                    new Column(
                        'created_at',
                        array(
                            'type' => Column::TYPE_INTEGER,
                            'unsigned' => true,
                            'size' => 11,
                            'after' => 'scope_id'
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
                    new Index('oauth_client_scopes_client_id_index', array('client_id'), null),
                    new Index('oauth_client_scopes_scope_id_index', array('scope_id'), null)
                ),
                'references' => array(
                    new Reference(
                        'oauth_client_scopes_client_id_foreign',
                        array(
                            'referencedSchema' => 'oauth2',
                            'referencedTable' => 'oauth_clients',
                            'columns' => array('client_id'),
                            'referencedColumns' => array('id')
                        )
                    ),
                    new Reference(
                        'oauth_client_scopes_scope_id_foreign',
                        array(
                            'referencedSchema' => 'oauth2',
                            'referencedTable' => 'oauth_scopes',
                            'columns' => array('scope_id'),
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
