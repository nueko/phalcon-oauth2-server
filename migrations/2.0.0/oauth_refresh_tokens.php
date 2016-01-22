<?php 

use Phalcon\Db\Column;
use Phalcon\Db\Index;
use Phalcon\Db\Reference;
use Phalcon\Mvc\Model\Migration;

/**
 * Class OauthRefreshTokensMigration_200
 */
class OauthRefreshTokensMigration_200 extends Migration
{
    /**
     * Define the table structure
     *
     * @return void
     */
    public function morph()
    {
        $this->morphTable('oauth_refresh_tokens', array(
                'columns' => array(
                    new Column(
                        'id',
                        array(
                            'type' => Column::TYPE_VARCHAR,
                            'notNull' => true,
                            'size' => 40,
                            'first' => true
                        )
                    ),
                    new Column(
                        'access_token_id',
                        array(
                            'type' => Column::TYPE_VARCHAR,
                            'notNull' => true,
                            'size' => 40,
                            'after' => 'id'
                        )
                    ),
                    new Column(
                        'expire_time',
                        array(
                            'type' => Column::TYPE_INTEGER,
                            'notNull' => true,
                            'size' => 11,
                            'after' => 'access_token_id'
                        )
                    ),
                    new Column(
                        'created_at',
                        array(
                            'type' => Column::TYPE_INTEGER,
                            'unsigned' => true,
                            'size' => 11,
                            'after' => 'expire_time'
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
                    new Index('PRIMARY', array('access_token_id'), null),
                    new Index('oauth_refresh_tokens_id_unique', array('id'), null)
                ),
                'references' => array(
                    new Reference(
                        'oauth_refresh_tokens_access_token_id_foreign',
                        array(
                            'referencedSchema' => 'oauth2',
                            'referencedTable' => 'oauth_access_tokens',
                            'columns' => array('access_token_id'),
                            'referencedColumns' => array('id')
                        )
                    )
                ),
                'options' => array(
                    'TABLE_TYPE' => 'BASE TABLE',
                    'AUTO_INCREMENT' => '',
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
