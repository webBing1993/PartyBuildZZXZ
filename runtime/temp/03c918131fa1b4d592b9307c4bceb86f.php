<?php if (!defined('THINK_PATH')) exit(); /*a:2:{s:64:"F:\PartyBuildDQ\public/../application/admin\view\user\index.html";i:1471572088;s:65:"F:\PartyBuildDQ\public/../application/admin\view\base\common.html";i:1477472194;}*/ ?>
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <!--<title><?php echo \think\Config::get('WEB_SITE_TITLE'); ?></title>-->
    <title>香市党建管理后台</title>
    <!-- CSS Files -->
    <link href="/static/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="/static/font-awesome/css/font-awesome.css" rel="stylesheet">
    <link href="/favicon.ico" type="image/x-icon" rel="shortcut icon">
    <style type="text/css">
        .profile-element {
            text-align: center;
        }
    </style>
    
<link href="/admin/css/plugins/iCheck/custom.css" rel="stylesheet">
<!-- Sweet Alert -->
<link href="/admin/css/plugins/sweetalert/sweetalert.css" rel="stylesheet">

    <!-- iCheck -->
    <link href="/admin/css/plugins/iCheck/custom.css" rel="stylesheet">
    <!-- Sweet Alert -->
    <link href="/admin/css/plugins/sweetalert/sweetalert.css" rel="stylesheet">
    <!-- Toastr style -->
    <link href="/admin/css/plugins/toastr/toastr.min.css" rel="stylesheet">
    <!-- CSS Files -->
    <link href="/admin/css/animate.css" rel="stylesheet">
    <link href="/admin/css/style.css" rel="stylesheet">
    <link href="/admin/css/main.css" rel="stylesheet">
    <script src="/admin/js/jquery-2.1.1.js"></script>
</head>
<body>
<div id="wrapper">
    <!-- 左侧系统菜单栏 -->
    <nav class="navbar-default navbar-static-side" role="navigation">
        <div class="sidebar-collapse">
            <ul class="nav metismenu" id="side-menu">
                <li class="nav-header">
                    <div class="dropdown profile-element">
                        <span><?php if($user['header'] == ''): ?><img alt="image" class="img-circle" src="/admin/images/profile_small.jpg" /><?php else: ?><img alt="image" style="width: 48px;height: 48px;" class="img-circle" src="<?php echo $user['header']; ?>" /><?php endif; ?></span>
                        <a data-toggle="dropdown" class="dropdown-toggle" href="#">
                            <span class="clear"> <span class="block m-t-xs"> <strong class="font-bold"><?php echo $user['username']; ?></strong></span>
                            <span class="text-muted text-xs block"><b class="caret"></b></span> </span> </a>
                        <ul class="dropdown-menu animated fadeInRight m-t-xs">
                            <li><a href="<?php echo Url('Index/editPassWord'); ?>">修改密码</a></li>
                            <!--<li><a href="contacts.html">联系方式</a></li>-->
                            <!--<li><a href="mailbox.html">消息中心</a></li>-->
                            <!--<li class="divider"></li>-->
                            <li><a href="<?php echo Url('Base/logout'); ?>">退出</a></li>
                        </ul>
                    </div>
                    <div class="logo-element">
                        MIN+
                    </div>
                </li>
                <?php if(is_array($__MENU__) || $__MENU__ instanceof \think\Collection): $i = 0; $__LIST__ = $__MENU__;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$menu): $mod = ($i % 2 );++$i;if(empty($menu['child']) || ($menu['child'] instanceof \think\Collection && $menu['child']->isEmpty())): if($menu['id'] == $subMenu['id']): ?><li class="active"><?php else: ?><li><?php endif; ?>
                <a href="<?php echo Url($menu['url']); ?>"><i class="fa <?php echo $menu['icon']; ?>"></i><span class="nav-label"><?php echo $menu['title']; ?></span></a></li>
                <?php else: if($subMenu['pid'] == $menu['id']): ?><li class="active"><?php else: ?><li><?php endif; ?>
                <a href="<?php echo Url($menu['url']); ?>"><i class="fa <?php echo $menu['icon']; ?>"></i> <span class="nav-label"><?php echo $menu['title']; ?></span>
                    <span class="fa arrow"></span>
                </a>
                <ul class="nav nav-second-level collapse <?php if($subMenu['pid'] == $menu['id']): ?>in<?php endif; ?>">
                    <?php if(is_array($menu['child']) || $menu['child'] instanceof \think\Collection): $i = 0; $__LIST__ = $menu['child'];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$child): $mod = ($i % 2 );++$i;if($subMenu['id'] == $child['id']): ?><li class="active"><?php else: ?><li><?php endif; ?><a href="<?php echo Url($child['url']); ?>"><?php echo $child['title']; ?></a></li>
                    <?php endforeach; endif; else: echo "" ;endif; ?>
                </ul></li>
                <?php endif; endforeach; endif; else: echo "" ;endif; ?>
                <!--<li class="landing_link">-->
                    <!--<a target="_blank" href="landing.html"><i class="fa fa-star"></i> <span class="nav-label">最新动态</span> <span class="label label-warning pull-right">NEW</span></a>-->
                <!--</li>-->
                <li class="special_link">
                    <a href="https://qy.weixin.qq.com" target="_blank"><i class="fa fa-wechat"></i> <span class="nav-label">微信管理平台</span></a>
                </li>
            </ul>
        </div>
    </nav>
    <!-- 右侧主体内容 -->
    <div id="page-wrapper" class="gray-bg">
        <!-- 中间头部 -->
        <div class="row border-bottom">
            <nav class="navbar navbar-static-top white-bg" role="navigation" style="margin-bottom: 0">
                <div class="navbar-header">
                    <a class="navbar-minimalize minimalize-styl-2 btn btn-primary " href="#"><i class="fa fa-bars"></i> </a>
                    <form role="search" class="navbar-form-custom" action="<?php echo Url('Index/search'); ?>">
                        <div class="form-group">
                            <input type="text" placeholder="搜索内容" class="form-control" name="top-search" id="top-search">
                        </div>
                    </form>
                </div>
                <ul class="nav navbar-top-links navbar-right">
                    <li>
                        <span class="m-r-sm text-muted welcome-message">欢迎使用<?php echo \think\Config::get('WEB_SITE_TITLE'); ?></span>
                    </li>
                    <li class="dropdown">
                        <a class="dropdown-toggle count-info" data-toggle="dropdown" href="#">
                            <i class="fa fa-envelope"></i>  <span class="label label-warning">0</span>
                        </a>
                    </li>
                    <li class="dropdown">
                        <a class="dropdown-toggle count-info" data-toggle="dropdown" href="#">
                            <i class="fa fa-bell"></i>  <span class="label label-primary">0</span>
                        </a>
                    </li>
                    <li>
                        <a href="<?php echo Url('Base/logout'); ?>">
                            <i class="fa fa-sign-out"></i> 退出
                        </a>
                    </li>
                    <li>
                        <a class="right-sidebar-toggle">
                            <i class="fa fa-tasks"></i>
                        </a>
                    </li>
                </ul>
            </nav>
        </div>
        <!-- 页面路径 -->
        
<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-lg-10">
        <h2>用户管理</h2>
        <ol class="breadcrumb">
            <li>
                <a href="index.html">Admin</a>
            </li>
            <li class="active">
                <strong>User</strong>
            </li>
        </ol>
    </div>
    <div class="col-lg-2">

    </div>
</div>

        <!-- 中间主体内容 -->
        
<div class="wrapper wrapper-content animated fadeInRight">
    <div class="row">
        <div class="col-lg-12">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5>后台用户列表</h5>
                    <div class="ibox-tools">
                        <a data-toggle="modal" class="btn btn-primary" href="#modal-form">添加新用户</a>
                        <div id="modal-form" class="modal fade" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-body">
                                        <form class="form-horizontal from-add-user" method="post" action="<?php echo Url('User/add'); ?>">
                                            <p>添加新用户</p>
                                            <div class="form-group"><label class="col-lg-2 control-label">账号</label>
                                                <div class="col-lg-10">
                                                    <input type="text" placeholder="账号只能为字母或数字" class="form-control" required="" name="username">
                                                </div>
                                            </div>
                                            <div class="form-group"><label class="col-lg-2 control-label">邮箱</label>
                                                <div class="col-lg-10">
                                                    <input type="email" placeholder="用户邮箱，用于找回密码等安全操作" class="form-control" required="" name="email">
                                                </div>
                                            </div>
                                            <div class="form-group"><label class="col-lg-2 control-label">密码</label>
                                                <div class="col-lg-10">
                                                    <input type="password" placeholder="至少6位数字或字母" class="form-control" required="" name="password">
                                                </div>
                                            </div>
                                            <div class="form-group"><label class="col-lg-2 control-label">确认密码</label>
                                                <div class="col-lg-10">
                                                    <input type="password" placeholder="确认密码" class="form-control" required="" name="repassword">
                                                </div>
                                            </div>
                                            <div class="form-group"><label class="col-lg-2 control-label">用户组</label>
                                                <div class="col-lg-10 checkbox-inline">
                                                    <?php if(is_array($authGroups) || $authGroups instanceof \think\Collection): $i = 0; $__LIST__ = $authGroups;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?>
                                                    <label><input class="auth_groups i-checks" type="radio" name="group_id" value="<?php echo $vo['id']; ?>" <?php if($i == '1'): ?>checked<?php endif; ?>> <?php echo $vo['title']; ?></label>
                                                    <?php endforeach; endif; else: echo "" ;endif; ?>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <div class="col-lg-offset-2 col-lg-10">
                                                    <button class="btn btn-primary ajax-post" type="submit" target-form="from-add-user">添加</button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div id="modal-form-3" class="modal fade" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-body">
                                <form class="form-horizontal form-edit-user" method="post" action="<?php echo Url('User/edit'); ?>">
                                    <p>编辑用户</p>
                                    <div class="form-group"><label class="col-lg-2 control-label">昵称</label>
                                        <div class="col-lg-10">
                                            <input type="text" placeholder="用户名会作为默认的昵称" class="form-control" required="" name="username">
                                        </div>
                                    </div>
                                    <div class="form-group"><label class="col-lg-2 control-label">邮箱</label>
                                        <div class="col-lg-10">
                                            <input type="email" placeholder="用户邮箱，用于找回密码等安全操作" class="form-control" required="" name="email">
                                        </div>
                                    </div>
                                    <div class="form-group"><label class="col-lg-2 control-label">密码</label>
                                        <div class="col-lg-10">
                                            <input type="password" placeholder="至少6位数字或字母" class="form-control" required="" name="password">
                                        </div>
                                    </div>
                                    <div class="form-group"><label class="col-lg-2 control-label">确认密码</label>
                                        <div class="col-lg-10">
                                            <input type="password" placeholder="确认密码" class="form-control" required="" name="repassword">
                                        </div>
                                    </div>
                                    <div class="form-group"><label class="col-lg-2 control-label">用户组</label>
                                        <div class="col-lg-10 checkbox-inline">
                                            <?php if(is_array($authGroups) || $authGroups instanceof \think\Collection): $i = 0; $__LIST__ = $authGroups;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?>
                                            <label><input class="auth_groups i-checks" type="radio" name="group_id" value="<?php echo $vo['id']; ?>"> <?php echo $vo['title']; ?></label>
                                            <?php endforeach; endif; else: echo "" ;endif; ?>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <div class="col-lg-offset-2 col-lg-10">
                                            <button class="btn btn-primary ajax-post" type="submit" target-form="form-edit-user">确认</button>
                                        </div>
                                    </div>
                                    <input type="hidden" name="id" value="">
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="ibox-content">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                            <tr>
                                <th><input class="i-checks check-all" type="checkbox"></th>
                                <th>UID</th>
                                <th>昵称</th>
                                <th>登录次数</th>
                                <th>最后登入时间 </th>
                                <th>最后登入IP</th>
                                <th>所属组</th>
                                <th>状态</th>
                                <th>操作</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php if(is_array($list) || $list instanceof \think\Collection): $i = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?>
                            <tr>
                                <td><input type="checkbox" class="i-checks ids" name="input[]"></td>
                                <td><?php echo $vo['id']; ?></td>
                                <td><?php echo $vo['nickname']; ?></td>
                                <td><?php echo $vo['login_total']; ?></td>
                                <td><span><?php echo time_format($vo['last_login_time']); ?></span></td>
                                <td><span><?php echo long2ip($vo['last_login_ip']); ?></span></td>
                                <td><?php if(!(empty($vo['roles']['0']) || ($vo['roles']['0'] instanceof \think\Collection && $vo['roles']['0']->isEmpty()))): ?><?php echo $vo->roles[0]->title; endif; ?></td>
                                <td><span class="label label-info"><?php echo $vo['status_text']; ?></span></td>
                                <td>
                                    <?php if($vo['status'] == '1'): ?>
                                        <a href="<?php echo Url('User/changeStatus?method=forbidUser&id='.$vo['id']); ?>" class="ajax-get">禁用</a>
                                    <?php else: ?>
                                        <a href="<?php echo Url('User/changeStatus?method=resumeUser&id='.$vo['id']); ?>" class="ajax-get">启用</a>
                                    <?php endif; ?>
                                    <a data-toggle="modal" class="authorize"  href="#modal-form-3" onclick="editUser('<?php echo $vo['id']; ?>','<?php echo $vo['nickname']; ?>','<?php echo $vo['email']; ?>','<?php if(!(empty($vo['roles']['0']) || ($vo['roles']['0'] instanceof \think\Collection && $vo['roles']['0']->isEmpty()))): ?><?php echo $vo->roles[0]->id; endif; ?>')">编辑</a>
                                    <a href="<?php echo Url('User/changeStatus?method=deleteUser&id='.$vo['id']); ?>" class="confirm ajax-del">删除</a>
                                </td>
                            </tr>
                            <?php endforeach; endif; else: echo "" ;endif; ?>
                            </tbody>
                            <tfoot>
                            <tr>
                                <td colspan="10">
                                    <div class="page"><?php echo $_page; ?></div>
                                </td>
                            </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

        <!-- 底部信息 -->
        <div class="footer">
            <div class="pull-right">
                版本 <strong>0.3</strong>
            </div>
            <div>
                <strong>Copyright</strong>杭州之图网络科技有限公司 &copy;2016-2017
            </div>
        </div>
    </div>
    <!-- 右侧工具栏 -->
</div>
<!-- Mainly scripts -->
<script src="/admin/js/bootstrap.min.js"></script>
<script src="/admin/js/plugins/metisMenu/jquery.metisMenu.js"></script>
<script src="/admin/js/plugins/slimscroll/jquery.slimscroll.min.js"></script>
<!-- Custom and plugin javascript -->
<script src="/admin/js/inspinia.js"></script>
<script src="/admin/js/plugins/pace/pace.min.js"></script>
<script src="/admin/js/common.js"></script>
<!-- Toastr script -->
<script src="/admin/js/plugins/toastr/toastr.min.js"></script>
<script src="/admin/js/plugins/sweetalert/sweetalert.min.js"></script>

<script src="/admin/js/plugins/iCheck/icheck.min.js"></script>
<script src="/admin/js/plugins/sweetalert/sweetalert.min.js"></script>
<script>
$(function(){
    $('.i-checks').iCheck({
        checkboxClass: 'icheckbox_square-green',
        radioClass: 'iradio_square-green'
    });
    $(".check-all").on("ifChanged",function(){
        $("tbody").find(".ids").iCheck("toggle");
    });
});
function editUser(uid, name, email, groupId) {
    var useForm = $("#modal-form-3");
    useForm.find("input[name='id']").val(uid);
    useForm.find("input[name='username']").val(name);
    useForm.find("input[name='email']").val(email);

    var groupCheck = useForm.find("input[name='group_id'][value="+groupId+"]");
    groupCheck.iCheck('check');
    groupCheck.attr("checked",true);
}
</script>

</body>
</html>
