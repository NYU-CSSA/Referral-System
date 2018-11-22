# NYU CSSA Referral System

## HOW TO

### Download

```shell
git clone git@github.com:NYU-CSSA/Referral-System.git
```

### Run it locally

```shell
php bin/console server:run
```

### deploy it to Heroku

```shell
# I forgot it. Refer to heroku tutorial.
```

## To our developers:

### What you should have already learned:
Don't be afraid of the complicated directory structures, 
I just learned and applied a PHP framework called [`Symfony`](https://symfony.com) to it.
Also I want to deploy this web app on [`Heroku`](https://heroku.com)

In order to get your hands dirty, you should go through the basic trial of both frameworks:

- For `Symfony`:
    - After reading [this](https://symfony.com/at-a-glance) you should have a simple idea of what does symfony do.
    - Then you should read through [this tutorial](https://symfony.com/doc/current/setup.html) and do it yourself by
     following the instructions, after which you should know how to install and use it to create an amazing website. 
     (Note: you should read all Chapter 1 - Chapter 5 of the [getting started tutorial](https://symfony.com/doc/current/index.html#gsc.tab=0))
    - Once you finish these simple readings, you will find this repository really really easy to understand.

- For `Heroku`:
    - Heroku is a company that can help us cheaply deploy a web app into the internet.
    Rather than purchasing a Virtual Machine to serve as a real server, we can directly upload our project source code
    and Heroku will run the codes in its "cloud". So we know our code is running in the cloud but we do not know which machine
    is running our code. As a result, we only need to focus on developing the logic and the contents, and all other things
    (such as network, firewall, maintaining the OS and VM) will be handled by Heroku.
    
    - After knowing what Heroku is, you should try [this little tutorial](https://devcenter.heroku.com/articles/getting-started-with-php)
    to deploy a really simple php project on your free account. Trust me it's not hard and it will be interesting.

    <!-- -[](https://medium.com/@luis.barros.nobrega/symfony-4-deploying-a-new-application-in-heroku-ada66f0592d1) -->

Have fun with the framework and the tool!

### Next step:

I have created a google cloud SQL project and we can learn how to operate it using PHP API so that
we can hook the app with the cloud database. I will check the resources below to find out how to connect
and how to "SQL" the Google Cloud!

- [How to connect to Google Cloud](https://cloud.google.com/sql/docs/mysql/connect-external-app)

- [Doctrine: a PHP tool to operate on databases](https://symfony.com/doc/current/doctrine.html)

- [Generate PHP Entities from existing database](https://symfony.com/doc/current/doctrine/reverse_engineering.html)

Also I will find out how do automatically deploy this app after each push to github. 
For now we can use our own accounts to deploy it.

## To users:

The NYU CSSA Referral System aims at:
> 建立一个nyu学生的简历信息数据库，在可以更好的收集了解nyu同学求职信息的同时，把这个简历库的使用权交给来招聘的企业。
> 同学可以填写信息和求职意向，企业可以登录简历库查找筛选同学的信息，建立学生和企业之间信息高效互通的渠道。
