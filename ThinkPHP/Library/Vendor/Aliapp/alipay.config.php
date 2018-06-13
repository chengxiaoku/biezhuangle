<?php
/* *
 * 配置文件
 * 版本：1.0
 * 日期：2016-06-06
 * 说明：
 * 以下代码只是为了方便商户测试而提供的样例代码，商户可以根据自己网站的需要，按照技术文档编写,并非一定要使用该代码。
 * 该代码仅供学习和研究支付宝接口使用，只是提供一个参考。
*/
 
//↓↓↓↓↓↓↓↓↓↓请在这里配置您的基本信息↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓
//合作身份者ID，签约账号，以2088开头由16位纯数字组成的字符串，查看地址：https://openhome.alipay.com/platform/keyManage.htm?keyType=partner
$alipay_config['partner']		= '2088911347181223';

//商户的私钥,此处填写原始私钥去头去尾，RSA公私钥生成：https://doc.open.alipay.com/doc2/detail.htm?spm=a219a.7629140.0.0.nBDxfy&treeId=58&articleId=103242&docType=1
$alipay_config['private_key']	= 'MIIEpAIBAAKCAQEAriFF/py0sf3mjyEJvQRwUtncLSskf87lKn7/pSb4IEcUvn/obSTPvMH2Vcs4l6jsW/1T2AcmePHnb7e7r2yg1m7qrihxbfiYorhOeqGHPzWoH4SzYGY25pWnaJ+un04/XK9ivYhHJecu4t1hLc0eDAna+YOR4lH+5CArxEE4efxDwg/GHItzGBkM0QxSF0L9zYPeVUUf0KuFwxvg9nJyMJeHvh58AI4xq1QQkMB0Coi+7SkORovixTnaTGF94j5sbqbvJzJbpG6gQdrhr9upURqYBT9aQPqkuhMTz+oCf54zgvMRoLEJReap+uk24Z+aUiXop3iqK9h64jp8qvc55QIDAQABAoIBAQCkEWnYC2MeSnNQ7Po2BNao2elhDcNoQVMgBWVvARbNouviyrX/EK5D1iX6lG6QR3PdAQZdB++yCYvdcasAEURCh2PMEQM/cPqwZyRa25OJdU8h03EIJYcZIG0KYVqxc5K0C/TnTF5tUjSVQa3s79Wd1jNi4hs0ubmGzOEwG5vRIteDJdSoOc4ID9HFKcyIGN38/XZInLqDwdP69MEwK6UsDSH4EZyQYd+4nl2DtfKyJv74jCGuIk98G2lxR02DxbXbn5dXvISRFKEisKBsctb4iusTjrYx83X+mTe7zNbm59zpSM2/y6YaT4/CVCIwLDYEf7S8A3Qx6TSSLljadB7JAoGBAN3AtAHhr+uPBxyQDpFFfRwx2HCL4pZX24zy1CgDc6nbZMQzOATZF3mWya2c1fs8T/tMbE4Qry7/KjIlV3jUE/f6Nqz54wMg1AgyN4Nw5/w1fbOrVLuGLyKq1Qyc/0UbadM4sAngaXRbR1W73eb0Mxt7VRnK9NG5FdvaMeaJ0iIXAoGBAMkFvTWCFF/4DWl604JgZ+N1Kox/gOMzVAX+a1eWKyDSKH+2kKVUXEKtyr6dcBRUKfNuJXR1SsggqJyqeQ5jGU9DDn9ruHsO4Sdbih2s8Q75l+Kw4wrqFTHfSZwmxHz775L0ZXGqCTjF81JZFHbBWDST2KvsE89Wq88uwmLmui1jAoGAUPoZwcYkc6SbODby2uHBOhaJry+l4rjal7Hk/2ejSRewyGTbxAlypRgHNbrb+Q6hNmdF9YvqLQNI6V7xflITqK5aYPgviejnBMcxtnH4hQUTjhS2sHPjqokvm5eJMDc+gb9lqXWljLP4bLpZ4giy0QpMnUqb9CnebZZUOpeBgKkCgYBS9vNoKg+OrCmc5vx0SqWV0r2Ezvq6ymxbLO3QcEHMgfAFBawdD/wj6OZMVV30hefqeAMtVVtPzWrJffIIZg4EEIDjReoVn8Z9H+KY7M9whsKQ5MBPuNyWgLk4oSdnDfOKiyNzDeJQdCJAUlOHz7Q/vuUlz4E9iV/PWlUPzHn6UQKBgQDVJDhaOtzKGgYXPL0YlrmW19x+POTGwA/ds9fvOEpE0m2AK6ATxihQD0hfFiqkhTArAvU3+8FcbomVJz1CCzcuYi7GEdQ6ee8Ohc8W6SwO9HWrovuFPLxrLR53OS6tNl3O6nQkyJcb1BZQmIgpJpf+5LQbtb32abF9fPPYxuUS1A==';

//支付宝的公钥，查看地址：https://openhome.alipay.com/platform/keyManage.htm?keyType=partner
$alipay_config['alipay_public_key']= 'MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAriFF/py0sf3mjyEJvQRwUtncLSskf87lKn7/pSb4IEcUvn/obSTPvMH2Vcs4l6jsW/1T2AcmePHnb7e7r2yg1m7qrihxbfiYorhOeqGHPzWoH4SzYGY25pWnaJ+un04/XK9ivYhHJecu4t1hLc0eDAna+YOR4lH+5CArxEE4efxDwg/GHItzGBkM0QxSF0L9zYPeVUUf0KuFwxvg9nJyMJeHvh58AI4xq1QQkMB0Coi+7SkORovixTnaTGF94j5sbqbvJzJbpG6gQdrhr9upURqYBT9aQPqkuhMTz+oCf54zgvMRoLEJReap+uk24Z+aUiXop3iqK9h64jp8qvc55QIDAQAB';

//异步通知接口
$alipay_config['service']= 'mobile.securitypay.pay';
//↑↑↑↑↑↑↑↑↑↑请在这里配置您的基本信息↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑

//签名方式 不需修改
$alipay_config['sign_type']    = strtoupper('RSA');

//字符编码格式 目前支持 gbk 或 utf-8
$alipay_config['input_charset']= strtolower('utf-8');

//ca证书路径地址，用于curl中ssl校验
//请保证cacert.pem文件在当前文件夹目录中
$alipay_config['cacert']    = getcwd().'/cacert.pem';

//访问模式,根据自己的服务器是否支持ssl访问，若支持请选择https；若不支持请选择http
$alipay_config['transport']    = 'http';
?>