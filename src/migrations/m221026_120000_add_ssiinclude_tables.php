<?php

namespace putyourlightson\blitz\migrations;

use craft\db\Migration;
use putyourlightson\blitz\Blitz;
use putyourlightson\blitz\records\CacheRecord;
use putyourlightson\blitz\records\SsiIncludeCacheRecord;
use putyourlightson\blitz\records\SsiIncludeRecord;

class m221026_120000_add_ssiinclude_tables extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp(): bool
    {
        $maxUriLength = Blitz::$plugin->settings->maxUriLength;

        if ($this->db->tableExists(CacheRecord::tableName())) {
            $this->alterColumn(CacheRecord::tableName(), 'uri', $this->string($maxUriLength)->notNull());
        }

        if (!$this->db->tableExists(SsiIncludeRecord::tableName())) {
            $this->createTable(SsiIncludeRecord::tableName(), [
                'id' => $this->primaryKey(),
                'uri' => $this->string($maxUriLength)->notNull(),
            ]);

            $this->createIndex(null, SsiIncludeRecord::tableName(), 'uri', true);
        }

        if (!$this->db->tableExists(SsiIncludeCacheRecord::tableName())) {
            $this->createTable(SsiIncludeCacheRecord::tableName(), [
                'cacheId' => $this->integer()->notNull(),
                'ssiIncludeId' => $this->integer()->notNull(),
                'PRIMARY KEY([[cacheId]], [[ssiIncludeId]])',
            ]);

            $this->addForeignKey(null, SsiIncludeCacheRecord::tableName(), 'cacheId', CacheRecord::tableName(), 'id', 'CASCADE', 'CASCADE');
            $this->addForeignKey(null, SsiIncludeCacheRecord::tableName(), 'ssiIncludeId', SsiIncludeRecord::tableName(), 'id', 'CASCADE', 'CASCADE');
        }

        return true;
    }

    /**
     * @inheritdoc
     */
    public function safeDown(): bool
    {
        echo self::class . " cannot be reverted.\n";

        return true;
    }
}
