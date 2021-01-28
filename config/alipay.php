<?php
return [
    'pay' => [
        // APPID
        'app_id' => '2021002115615034',
        // 支付宝 支付成功后 主动通知商户服务器地址  注意 是post请求
        'notify_url' => 'http://192.168.0.110:9555/api/home/ali_pay_ntify',
        // 支付宝 支付成功后 回调页面 get
//        'return_url' => 'http://192.168.0.110:9528/#/pay_success',
        // 公钥（注意是支付宝的公钥，不是商家应用公钥）
        'ali_public_key' => 'MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAjCi6QYRBObwwCpXIpPJhN5GMYGg61QvXw7M/m+Gk20ASv5foejrv2NR35NHIuUlxWBO/9ajm1FVLkpiS8CJz7IX+3zuUN6QtN3dOmmPq3FPA+oxwsJpE5SZ009SarAwNXP8dH8Mn/SXkLSkTwTvlotUEZ1DK35RGy8vZv6oIY2EOPsZl0gM/GjWt6eVY+qJ4cXKZ5gkWuWkK7XIZQV9n+snnuYfaqmDwvDrsvIzoYha93z6KhiPpRWypQDgX4kQnu5J7rWEBKa6FsmSnWB6qlXQu+F7spYsSi5vBbfYMxKZzQ7nfmRIBLpzC2Qkf5eSw6BmHQPT1baadim7/F8qy0QIDAQAB',
        // 加密方式： **RSA2** 私钥 商家应用私钥
        'private_key' => 'MIIEowIBAAKCAQEAjCi6QYRBObwwCpXIpPJhN5GMYGg61QvXw7M/m+Gk20ASv5foejrv2NR35NHIuUlxWBO/9ajm1FVLkpiS8CJz7IX+3zuUN6QtN3dOmmPq3FPA+oxwsJpE5SZ009SarAwNXP8dH8Mn/SXkLSkTwTvlotUEZ1DK35RGy8vZv6oIY2EOPsZl0gM/GjWt6eVY+qJ4cXKZ5gkWuWkK7XIZQV9n+snnuYfaqmDwvDrsvIzoYha93z6KhiPpRWypQDgX4kQnu5J7rWEBKa6FsmSnWB6qlXQu+F7spYsSi5vBbfYMxKZzQ7nfmRIBLpzC2Qkf5eSw6BmHQPT1baadim7/F8qy0QIDAQABAoIBACh6TqhDOM8iwUEdVvAEK/1vZHonP+5tWCflqZYsSX8kdwWsKnC6erBFVEbaKz0Pr9M/CO46lEd4RrYUqEL/wDjrzdrMixayrHhbVXETMC3nZlE1pAns077WJ2FSAkVzyZw09UVKCE981PQR6+mfkcc/++CWnbCKUxPiUIWg5oFEhEz/pKvCVmLKDCo2+Zq525YChml7SjZ/B1ypNugd9sMRWUqbYZKxLRcoyCEnhe42HYyrxAs77zdUrPIHHxt1UJznMKbiQe4m2wh9fnCpYVqPqnYrSueaEXvT1j5FF6NY+MwEQy838om9goLjOMDqWDJcQ85yf1+STsUGvuINO8ECgYEA4c2ZT7qkKs5OMyldudYubxl6oszhdeWaM//NYYL0iPTe/Brlqy6IFj+f9S7h53RgR26BfDE4v7wEgH6/HB24s7PaivQEgwyEWUvfsv2YX7EJxCQXxvY0MN4YxxsI+n3V/iT+KArl/+4cbMwH/wSb2UAUHSkjCiPIdoBjyV3gA+kCgYEAnucX78f9PwyjjeaduZpE2tSo+7Xd9UjQqt2s5KGHRtUB5UbXzHma75v5GgSCUTvj8GrnmRdkBXlJJ5i4Cck4CiKHbgnJOlTTYHg0Kq8dzoLa2xE6UuqHKr6oZwBHoHq3rTKTGMVvmIpkLB2r72o0EMP/3dzs2eFOJ19ziNEqbqkCgYBAzThKYU3r2vkmRaDYTFdXGwDO5+1sYFA4zBis+2AiTeQQhceTsO7tM6U8QBAk1Iks9tHCSn32yIaiOb2u2/i92cfGSPFiip0Q4213eL/Z9nzPBWytdMrVm2eQ/Hk/Kg3XudYWt8n6AOO3dXZ2AWSYnIMpOz0LE+nb2EzK5V38wQKBgG4GURqj/QPk+nJMljVnTfm/eeofuToXWvSUXRxJg+NVpeRdMxrPsKnr84VCTgF1WJSlUQs9mPH5Ut/p7LRJ8VWDJwbYG88Z4OpREL3lVtwg2u0IdrvmzASa0vvOw8mobvmrpRJn0qdqW4X4XY5+mOJq9AVo7U7HpFe6zcKUIa9RAoGBAIHuFSTLJIThE6soOK8nal8JQcgcx+Q4h+cyu2wmf+slCDjJcXZmT9T8wwQ7WwrFFdi6c+3Xa1CrjEcV5YY86ULzvwL0qtla7uEusN6EoR0JXABVE6QDtXJwh7XbPW+6mrfsySmbE+FfjPUPEWJH0VLbP6YS0MbMIi7dJBl4upxr',
        'log' => [ // optional
            'file' => '../storage/logs/alipay.log',
            'level' => 'debug', // 建议生产环境等级调整为 info，开发环境为 debug
            'type' => 'daily', // optional, 可选 daily.
            'max_file' => 10, // optional, 当 type 为 daily 时有效，默认 30 天
        ],
        'http' => [
            'timeout' => 5.0,
            'connect_timeout' => 5.0,
            // 更多配置项请参考 [Guzzle](https://guzzle-cn.readthedocs.io/zh_CN/latest/request-options.html)
        ],
        'mode' => 'dev', // optional,设置此参数，将进入沙箱模式
    ]
];