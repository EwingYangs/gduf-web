<?php

return [
    'adminEmail' => 'admin@example.com',
    'trem' => '2017-2018-2',//当前的学期
    'gdufLoginUrl' => 'http://jwxt.gduf.edu.cn/jsxsd/xk/LoginToXk',//教务系统登录地址
    'gdufScoreUrl' => 'http://jwxt.gduf.edu.cn/jsxsd/kscj/cjcx_list',//成绩查询地址
    'gdufbooksearchUrl' => 'http://218.192.12.92/NTRdrBookRetr.aspx',//图书馆搜索地址
    'gdufbookdetailUrl' => 'http://api.douban.com/book/subject/isbn/',//图书馆搜索地址
    'gdufbooklocalUrl' => 'http://218.192.12.92/GetlocalInfoAjax.aspx',//图书馆藏馆信息地址
    'gdufbookImageUrl' => 'http://218.192.12.92/NTRdrBookRetrAjaxImage.aspx',//图书馆图片地址
    'gdufclassroomUrl' => 'http://jwxt.gduf.edu.cn/jsxsd/kbcx/kbxx_classroom_ifr',//自习室查询地址
    'gduflessonUrl' => 'http://jwxt.gduf.edu.cn/jsxsd/xskb/xskb_list.do',//课表查询地址
    'gdufgetcurrentfeeUrl' => 'http://dylg.idianfun.com/TP32/index.php/Weixin/Electric/getList.html',//水电费查询地址
    'pageSize' => 5, //默认每页显示5条记录

    //智校园接口（新版本使用）
    'authUserUrl' => 'http://jwxt.gduf.edu.cn/app.do?method=authUser',
    'getCjcxUrl' => 'http://jwxt.gduf.edu.cn/app.do?method=getCjcx',
    'getKbcxAzcUrl' => 'http://jwxt.gduf.edu.cn/app.do?method=getKbcxAzc'
];
