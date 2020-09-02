<?php

use AmoCRM\Events\PhoneCall;
use AmoCRM\Request\ParamsBag;

class PhoneCallMock extends PhoneCall
{
    public $mockUrl;
    public $mockParameters;
    public $mockModified;

    protected function postRequest($url, $parameters = [])
    {
        $this->mockUrl = $url;
        $this->mockParameters = $parameters;
        $this->mockModified = null;

        return [
            'items' => [
                [
                    'uid' => '26a006b0-5732-491a-997e-72b3268b876e',
                    'phone_number' => '+79991112233',
                    'users' => [10, 20],
                    'element_type' => null,
                    'element_id' => null
                ]
            ]
        ];
    }
}

class PhoneCallListMock extends PhoneCallMock
{
    protected function postRequest($url, $parameters = [])
    {
        $this->mockUrl = $url;
        $this->mockParameters = $parameters;
        $this->mockModified = null;

        return [
            'items' => [
                [
                    'uid' => '26a006b0-5732-491a-997e-72b3268b876e',
                    'phone_number' => '+79991112233',
                    'users' => [10],
                    'element_type' => null,
                    'element_id' => null
                ],
                [
                    'uid' => '26a006b0-491a-5732-997e-72b3268b876e',
                    'phone_number' => '+79998887766',
                    'users' => [10, 20, 30],
                    'element_type' => null,
                    'element_id' => null
                ]
            ]
        ];
    }
}

class PhoneCallTest extends TestCase
{
    /**
     * @var null|PhoneCallMock
     */
    private $model = null;

    /**
     * @var null|PhoneCallListMock
     */
    private $modelList = null;

    public function setUp()
    {
        $paramsBag = new ParamsBag();
        $this->model = new PhoneCallMock($paramsBag);
        $this->modelList = new PhoneCallListMock($paramsBag);
    }

    /**
     * @dataProvider fieldsProvider
     * @param string $field
     * @param string|int|array $value
     * @param string|int|array $expected
     */
    public function testFields($field, $value, $expected)
    {
        $this->model[$field] = $value;
        $this->assertEquals($this->model[$field], $expected);
    }

    public function testSend()
    {
        $expected = [
            'add' => [
                [
                    'type' => 'phone_call',
                    'phone_number' => '+79991112233',
                    'users' => [10],
                ]
            ]
        ];

        $this->model['phone_number'] = '+79991112233';
        $this->model['users'] = [10];

        $this->assertEquals('+79991112233', $this->model->send([$this->model])['phone_number']);
        $this->assertEquals('/api/v2/events/', $this->model->mockUrl);
        $this->assertEquals($expected, $this->model->mockParameters);
        $this->assertNull($this->model->mockModified);

        $expected = [
            'add' => [
                [
                    'type' => 'phone_call',
                    'phone_number' => '+79991112233',
                    'users' => [10],
                ],
                [
                    'type' => 'phone_call',
                    'phone_number' => '+79998887766',
                    'users' => [10, 20, 30],
                ]
            ]
        ];

        $secondModel = clone $this->model;
        $secondModel['phone_number'] = '+79998887766';
        $secondModel['users'] = [10, 20, 30];

        $this->assertCount(2, $this->modelList->send([$this->model, $secondModel]));
        $this->assertEquals('/api/v2/events/', $this->modelList->mockUrl);
        $this->assertEquals($expected, $this->modelList->mockParameters);
        $this->assertNull($this->modelList->mockModified);
    }

    public function fieldsProvider()
    {
        return [
            ['phone_number', '+79991112233', '+79991112233'],
            ['users', [10, 20, 30], [10, 20, 30]],
        ];
    }
}