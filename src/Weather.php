<?php
/**
 * Created by PhpStorm.
 * Author: Nason
 * Date: 2021/4/13
 * Time: 11:38
 *
 *                    _ooOoo_
 *                   o8888888o
 *                   88" . "88
 *                   (| -_- |)
 *                    O\ = /O
 *                ____/`---'\____
 *              .   ' \\| |// `.
 *               / \\||| : |||// \
 *             / _||||| -:- |||||- \
 *               | | \\\ - /// | |
 *             | \_| ''\---/'' | |
 *              \ .-\__ `-` ___/-. /
 *           ___`. .' /--.--\ `. . __
 *        ."" '< `.___\_<|>_/___.' >'"".
 *       | | : `- \`.;`\ _ /`;.`/ - ` : | |
 *         \ \ `-. \_ __\ /__ _/ .-` / /
 * ======`-.____`-.___\_____/___.-`____.-'======
 *                    `=---='
 *
 * .............................................
 *          佛祖保佑             永无BUG
 */

namespace Nason\Weather;


use GuzzleHttp\Client;
use Nason\Weather\Exceptions\HttpException;
use Nason\Weather\Exceptions\InvalidArgumentException;

class Weather
{

    protected $key;

    protected $guzzleOptions = [];


    public function __construct($key)
    {
        $this->key = $key;
    }


    public function getHttpClient()
    {
        return new Client($this->guzzleOptions);
    }


    public function setGuzzleOptions(array $options)
    {
        $this->guzzleOptions = $options;
    }

    public function getLiveWeather($city, $format = 'json')
    {
        return $this->getWeather($city, 'base', $format);
    }

    public function getForecastsWeather($city, $format = 'json')
    {
        return $this->getWeather($city, 'all', $format);
    }

    public function getWeather($city,$type = 'base',$format = 'json')
    {
        // 高德地图天气查询 url
        $url = "https://restapi.amap.com/v3/weather/weatherInfo";

        if (!\in_array(\strtolower($format), ['json', 'xml'])) {
            throw new InvalidArgumentException('Invalid response format: ' . $format);
        }

        if (!\in_array(\strtolower($type), ['base', 'all'])) {
            throw new InvalidArgumentException('Invalid type value(base/all): ' . $type);
        }

        $query = array_filter([
            'key' => $this->key,
            'city' => $city,
            'output' => $format,
            'extensions' => $type,
        ]);

        try {
            $response = $this->getHttpClient()->get($url, [
                'query' => $query
            ])->getBody()->getContents();
            return 'json' === $format ? \json_decode($response, true) : $response;
        } catch (\Exception $e) {
            throw new HttpException($e->getMessage(), $e->getCode(), $e);
        }
    }
}