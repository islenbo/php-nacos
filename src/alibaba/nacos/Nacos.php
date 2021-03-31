<?php


namespace alibaba\nacos;


use alibaba\nacos\util\LogUtil;

/**
 * Class Nacos
 * @author suxiaolin
 * @package alibaba\nacos
 */
class Nacos
{
    /** @var NacosClientInterface */
    private static $clientClass;

    public static function init($host, $env, $dataId, $group, $tenant)
    {
        static $client;
        if ($client == null) {
            NacosConfig::setHost($host);
            NacosConfig::setEnv($env);
            NacosConfig::setDataId($dataId);
            NacosConfig::setGroup($group);
            NacosConfig::setTenant($tenant);

            if (getenv("NACOS_ENV") == "local") {
                LogUtil::info("nacos run in dummy mode");
                self::$clientClass = new DummyNacosClient();
            } else {
                self::$clientClass = new NacosClient();
            }

            $client = new self();
        }
        return $client;
    }

    public function runOnce()
    {
        return self::$clientClass::get(NacosConfig::getEnv(), NacosConfig::getDataId(), NacosConfig::getGroup(), NacosConfig::getTenant() . '');
    }

    public function listener(int $loop = 0)
    {
        self::$clientClass::listener(NacosConfig::getEnv(), NacosConfig::getDataId(), NacosConfig::getGroup(), '', NacosConfig::getTenant() . '', $loop);
        return $this;
    }

}