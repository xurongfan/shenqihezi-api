<?php
return [
    'pay' => [
        // APPID
        'app_id' => '2021002115615034',
        // 支付宝 支付成功后 主动通知商户服务器地址  注意 是post请求
        'notify_url' => 'http://192.168.0.110:9555/api/home/ali_pay_ntify',
        // 支付宝 支付成功后 回调页面 get
        'return_url' => 'http://192.168.0.110:9528/#/pay_success',
        // 公钥（注意是支付宝的公钥，不是商家应用公钥）
        'ali_public_key' => 'MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAg2f8RUcHCMNq+Oe1sdwE+RRyWMmMAAB/wWEvsgWlUGNbYN/nHbie8fScNji7+qLI2r3MrK4Lk20koqWv3S+BYqZmCjgtzKAp9rsm9mkYctEazUs64f0+aEYRgYF+kTq6f23ARQNwA2El0V3xGeE4wr9yZQVaZin2CD7ogHvJ+syc23/OoZpiKjiOZz3O5GXGNAqV7sB4wLyqOcAN9G26qVpjGrOCziXf9I96ycV5pVDLqW3eX98eNwGbvQNhl/FzDofAtK5oyY1IVpLk8oK7nT0gbOIC0nj0mmPiYaXMJzG4gUdKGS2a4RDhmetebVpgYfN0HD8aIlYbu3IsqlFuiQIDAQAB',
        // 加密方式： **RSA2** 私钥 商家应用私钥
        'private_key' => 'MIIEvQIBADANBgkqhkiG9w0BAQEFAASCBKcwggSjAgEAAoIBAQCDZ/xFRwcIw2r457Wx3AT5FHJYyYwAAH/BYS+yBaVQY1tg3+cduJ7x9Jw2OLv6osjavcysrguTbSSipa/dL4FipmYKOC3MoCn2uyb2aRhy0RrNSzrh/T5oRhGBgX6ROrp/bcBFA3ADYSXRXfEZ4TjCv3JlBVpmKfYIPuiAe8n6zJzbf86hmmIqOI5nPc7kZcY0CpXuwHjAvKo5wA30bbqpWmMas4LOJd/0j3rJxXmlUMupbd5f3x43AZu9A2GX8XMOh8C0rmjJjUhWkuTygrudPSBs4gLSePSaY+JhpcwnMbiBR0oZLZrhEOGZ615tWmBh83QcPxoiVhu7ciyqUW6JAgMBAAECggEAG0l3mEc0jbxOtVsgXsuqeLKnQAqyOLnhzZ1J0zTa7EOFkStNid1xnBrIaXYEivpMrSemneESZE3QwrmXOF4KBxs4qzyZPhmwPR0F7TtLqWQEpcbOdmkpEWwjbHogdzXzzkxTFcGt+/WrbbuNOmuLZt1ses/OwtJJ1dKCY1PRUHPogLdKA+ka4E3Wms7UECSF7WId826lEe8znJAzyIy0JnVovp+dsdTRXZ8ZRLZUYKzENaPTd3IU007gviwiJc62491VFiGg9SDPXvXlewYqCa9XLH9klJzbc9C7d1R35lFtWf9fO7C71cVlNIutL7yHwf8A3VT7tnDPNEa/U9sSgQKBgQDoB0GEaiVDd0iSnErfCC0yhL485LrlDaeloUe+oUTUQQN0iU5cS+fIPd5UyKIuIG+p/Ph2nrietyj6TglZdBTiYQfuKVAYWCKfjY3yUe7jMloN3AQGNs8LbFK9tfxeWi2hcXFlH/whUgg8lGlFZHOxSx/3mVNQn/r31S9EZ0r+GQKBgQCQ+3MUL/y7pqPunOQsNOxL8OkTUKCbwjN7TEUP3GVwkmjRQnnRXIRF58e5THvbGpzn6n80MC6QGi2K+N0+rK1a4s5D/D2Bi2YsQpvvnnVjmoJxUgI3JeXqv1dqiKMHYOcNT0peSO8eRyIr9FrOdbqBmBTgVePiuhVbmPLfoo8h8QKBgFJrwWo4CmScyrrJqg7v6J45aRpYZctXvmWnlMSypLCBJ6kN3TgL/pmy4Hddjb605vWLVvdMCmjWx0ei0M9l3MVNknOXWUxMgoAK2JFraWBrUnH55bQPZBy6remV67/YaL0gfxdc9UMg0Kw0S/DKbg5ckU5yuJW7Pnsqz0+NIaaBAoGAYIKiBmwzRw8UzUAvO6Y3Lg4+eRcQ8t6Buq/4wSgrdpZfo/0mblGx038JGrZpNF8w18jnyGvScyaZ7orfbFsyQu/78pG0t8l9yDTG7OmEpzsxXhIDW4ak2HFq1YYDUGQXKmr/zGeslXEwtCXDVPBVqPL94qAKROEuWn0kH+5sbWECgYEA0A8GBNICjiUUpGhjX2UvCYWxtMwuFZ/NUqjeyr2O4zTFDN7pOK7B7sZr+Y/ia2dULZPiJZgWAEpj9TC+wfdfO3S84Nbt3ORmqbY3KjyleE4YyVhPuqpH5jLuLCQtP9Pq85k7ri9Qyb/Bc7UDu1xH1iI3gGsvTTOYzHDGx9YNjUo=',
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