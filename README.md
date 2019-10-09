# Supervisor Dashboard

Supervisor 进程控制台，集中管理服务器进程、定时任务和在服务器上执行命令。它需要配合 Supervisor Agent 项目一起使用。

Supervisor Dashboard 的主要作用

- 管理进程（eg. task/worker）。具有添加、删除、修改、查找、查看日志等功能；
- 管理定时任务。具有可指定执行用户、内置常用执行周期、中断执行、保留最近执行日志等特性；
- 执行命令或脚本。

与 Supervisor Agent 之间的关系

![The relationship between Supervisor Dashboard and Supervisor Agent](resouces/img/supervisor-dashboard.jpg)

Supervisor Dashboard 用于集中管理其他服务器的进程、定时任务和执行命令。而 Supervisor Agent 需安装在每一台服务器节点上，
它主要的作用是扩展 Supervisor 的功能，让它能够管理定时任务以及执行一次性的命令。

## 适用对象

- 程序开发人员
- 运维工程师

## 项目依赖

- PHP 7.0+
- Phalcon Framework 3.4+
- MySQL 5.6+

Phalcon 是一个灵活性高、耦合度低的框架。该项目并没有使用过多参考一些最佳实践，只是希望简单问题简单处理，所以如果你看到一些非主流做法，请不要惊讶。
如果你有好的想法，希望您能跟分享，谢谢～

## 安装

PHP 环境安装

```bash

```

项目初始化

```apple js

```


