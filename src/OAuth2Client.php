<?php

namespace AmoCRM;

use AmoCRM\Request\CurlHandle;
use AmoCRM\Request\ParamsBag;
use AmoCRM\Helpers\Fields;

/**
 * Class OAuth2Client
 *
 * Авторизация в AmoCRM с использованием OAuth2 (https://www.amocrm.ru/developers/content/oauth/switch-over-oauth)
 *
 * @property \AmoCRM\Models\Account $account
 * @property \AmoCRM\Models\Call $call
 * @property \AmoCRM\Models\Catalog $catalog
 * @property \AmoCRM\Models\CatalogElement $catalog_element
 * @property \AmoCRM\Models\Company $company
 * @property \AmoCRM\Models\Contact $contact
 * @property \AmoCRM\Models\Customer $customer
 * @property \AmoCRM\Models\CustomersPeriods $customers_periods
 * @property \AmoCRM\Models\CustomField $custom_field
 * @property \AmoCRM\Models\Lead $lead
 * @property \AmoCRM\Models\Links $links
 * @property \AmoCRM\Models\Note $note
 * @property \AmoCRM\Models\Pipelines $pipelines
 * @property \AmoCRM\Models\Task $task
 * @property \AmoCRM\Models\Transaction $transaction
 * @property \AmoCRM\Models\Unsorted $unsorted
 * @property \AmoCRM\Models\Webhooks $webhooks
 * @property \AmoCRM\Models\Widgets $widgets
 * @property \AmoCRM\Events\PhoneCall $phone_call
 */
class OAuth2Client extends Client
{
    private $curlHandle;

    /**
     * @param string $domain Поддомен или домен amoCRM
     * @param string $accessToken Access токен (https://www.amocrm.ru/developers/content/oauth/step-by-step)
     * @param string|null $proxy Прокси сервер для отправки запроса
     */
    public function __construct($domain, $accessToken, $proxy = null)
    {
        parent::__construct($domain, '', '', $proxy);
        if (strpos($domain, '.') === false) {
            $domain = sprintf('%s.amocrm.ru', $domain);
        }
        $this->parameters = new ParamsBag();
        $this->parameters->addAuth('domain', $domain);
        $this->parameters->addAuth('access_token', $accessToken);
        if ($proxy !== null) {
            $this->parameters->addProxy($proxy);
        }
        $this->fields = new Fields();
        $this->curlHandle = new CurlHandle();
    }
}
