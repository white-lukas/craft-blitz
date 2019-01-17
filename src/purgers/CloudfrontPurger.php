<?php
/**
 * @copyright Copyright (c) PutYourLightsOn
 */

namespace putyourlightson\blitz\purgers;

use Craft;
use GuzzleHttp\Exception\BadResponseException;

/**
 * @property mixed $settingsHtml
 */
class CloudfrontPurger extends BasePurger
{
    // Constants
    // =========================================================================

    const API_ENDPOINT = 'https://api.cloudflare.com/client/v4/';

    // Properties
    // =========================================================================

    /**
     * @var string
     */
    public $email;

    /**
     * @var string
     */
    public $apiKey;

    /**
     * @var string
     */
    public $zoneId;

    // Static
    // =========================================================================

    /**
     * @inheritdoc
     */
    public static function displayName(): string
    {
        return Craft::t('blitz', 'Cloudfront Purger');
    }

    // Public Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public function attributeLabels(): array
    {
        return [
            'apiKey' => Craft::t('blitz', 'API Key'),
            'zoneId' => Craft::t('blitz', 'Zone ID'),
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['apiKey', 'email', 'zoneId'], 'required'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function purgeUrls(array $urls)
    {
        $this->_purge(['files' => $urls]);
    }

    /**
     * @inheritdoc
     */
    public function purgeAll()
    {
        $this->_purge(['purge_everything' => true]);
    }

    /**
     * @inheritdoc
     */
    public function getSettingsHtml()
    {
        return Craft::$app->getView()->renderTemplate('blitz/_purgers/cloudflare/settings', [
            'purger' => $this,
        ]);
    }

    // Private Methods
    // =========================================================================

    /**
     * Sends a DELETE request to the API.
     *
     * @param array|null $options
     */
    private function _purge(array $options = [])
    {
        $client = Craft::createGuzzleClient([
            'base_uri' => self::API_ENDPOINT,
            'headers'  => [
                'Content-Type' => 'application/json',
                'X-Auth-Email' => $this->email,
                'X-Auth-Key'   => $this->apiKey,
            ],
        ]);

        try {
            $client->request(
                'DELETE',
                'zones/'.$this->zoneId.'/purge_cache',
                $options
            );
        }
        catch (BadResponseException $e) { }
    }
}