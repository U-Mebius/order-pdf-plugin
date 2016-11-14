<?php
/*
 * This file is part of the Order pdf plugin
 *
 * Copyright (C) 2016 LOCKON CO.,LTD. All Rights Reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\ORM\Tools\SchemaTool;
use Eccube\Application;
use Doctrine\ORM\EntityManager;
use Plugin\OrderPdf\Utils\Version;

/**
 * Class Version201611111300.
 */
class Version201611111300 extends AbstractMigration
{
    /**
     * @var string table name
     */
    const TABLE = 'plg_order_pdf';

    /**
     * @var array plugin entity
     */
    protected $entities = array(
        'Plugin\OrderPdf\Entity\OrderPdf',
    );

    /**
     * Up method
     *
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        if (Version::isSupport()) {
            $this->createOrderPdf($schema);
        } else {
            $this->createOrderPdfForOldVersion($schema);
        }
    }

    /**
     * Down method
     *
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        if (Version::isSupport()) {
            $app = Application::getInstance();
            $meta = $this->getMetadata($app['orm.em']);
            $tool = new SchemaTool($app['orm.em']);
            $schemaFromMetadata = $tool->getSchemaFromMetadata($meta);
            // テーブル削除
            foreach ($schemaFromMetadata->getTables() as $table) {
                if ($schema->hasTable($table->getName())) {
                    $schema->dropTable($table->getName());
                }
            }
        } else {
            if ($schema->hasTable(self::TABLE)) {
                $schema->dropTable(self::TABLE);
            }
        }
    }

    /**
     * Create order pdf table.
     *
     * @param Schema $schema
     *
     * @return bool
     */
    protected function createOrderPdf(Schema $schema)
    {
        if ($schema->hasTable(self::TABLE)) {
            return true;
        }

        $app = Application::getInstance();
        $em = $app['orm.em'];
        $classes = array(
            $em->getClassMetadata('Plugin\OrderPdf\Entity\OrderPdf'),
        );
        $tool = new SchemaTool($em);
        $tool->createSchema($classes);

        return true;
    }

    /**
     * Create order pdf for old version.
     *
     * @param Schema $schema
     */
    protected function createOrderPdfForOldVersion(Schema $schema)
    {
        $table = $schema->createTable(self::TABLE);
        $table->addColumn('member_id', 'integer', array(
            'unsigned' => true,
        ));

        $table->addColumn('issue_date', 'datetime', array(
            'notnull' => false,
        ));

        $table->addColumn('title', 'string', array(
            'notnull' => false,
            'length' => 50,
        ));

        $table->addColumn('message1', 'string', array(
            'notnull' => false,
            'length' => 30,
        ));

        $table->addColumn('message2', 'string', array(
            'notnull' => false,
            'length' => 30,
        ));

        $table->addColumn('message3', 'string', array(
            'notnull' => false,
            'length' => 30,
        ));

        $table->addColumn('note1', 'string', array(
            'notnull' => false,
            'length' => 50,
        ));

        $table->addColumn('note2', 'string', array(
            'notnull' => false,
            'length' => 50,
        ));

        $table->addColumn('note3', 'string', array(
            'notnull' => false,
            'length' => 50,
        ));

        $table->addColumn('del_flg', 'smallint', array(
            'unsigned' => true,
            'default' => 0,
        ));

        $table->addColumn('create_date', 'datetime', array(
            'unsigned' => false,
        ));

        $table->addColumn('update_date', 'datetime', array(
            'notnull' => true,
            'unsigned' => false,
        ));

        $table->setPrimaryKey(array('member_id'));
    }
    /**
     * Get metadata.
     *
     * @param EntityManager $em
     *
     * @return array
     */
    protected function getMetadata(EntityManager $em)
    {
        $meta = array();
        foreach ($this->entities as $entity) {
            $meta[] = $em->getMetadataFactory()->getMetadataFor($entity);
        }

        return $meta;
    }
}
