<?php if (!defined('THINK_PATH')) exit(); /*a:2:{s:64:"F:\PartyBuildDQ\public/../application/admin\view\auth\index.html";i:1477992314;s:65:"F:\PartyBuildDQ\public/../application/admin\view\base\common.html";i:1477472194;}*/ ?>
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
<link href="/admin/css/plugins/summernote/summernote.css" rel="stylesheet">
<link href="/admin/css/plugins/summernote/summernote-bs3.css" rel="stylesheet">
<link href="/admin/css/style.css" rel="stylesheet">


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
                <strong>Auth</strong>
            </li>
        </ol>
    </div>
</div>

        <!-- 中间主体内容 -->
        
<div class="wrapper wrapper-content animated fadeInRight">
    <div class="row">
        <div class="col-lg-12">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5>管理组列表</h5>
                    <div class="ibox-tools">
                        <a data-toggle="modal" class="btn btn-primary" href="#modal-form">添加新管理组</a>
                        <div id="modal-form" class="modal fade" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-body">
                                        <form class="form-horizontal form-add-auth" method="post" action="<?php echo Url('Auth/createGroup'); ?>" enctype="application/x-www-form-urlencoded">
                                            <p>添加新管理组</p>
                                            <div class="form-group">
                                                <label class="col-lg-2 control-label">用户组</label>
                                                <div class="col-lg-10">
                                                    <input type="text" class="form-control" required="" name="title">
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="col-lg-2 control-label">描述</label>
                                                <div class="col-lg-10">
                                                    <textarea type="text" class="form-control" name="description"></textarea>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <div class="col-lg-offset-2 col-lg-10">
                                                    <button class="btn btn-primary ajax-post" type="submit" target-form="form-add-auth">添加</button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div id="modal-form-4" class="modal fade" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div  class="modal-body">
                                <form class="form-horizontal form-edit-auth" method="post" action="<?php echo Url('Auth/edit'); ?>" enctype="application/x-www-form-urlencoded">
                                    <p>编辑管理组</p>
                                    <div class="form-group">
                                        <label class="col-lg-2 control-label">用户组</label>
                                        <div class="col-lg-10">
                                            <input type="text" class="form-control" required="" name="title">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-lg-2 control-label">描述</label>
                                        <div class="col-lg-10">
                                            <textarea type="text" class="form-control" name="description"></textarea>
                                        </div>
                                    </div>
                                    <input type="hidden" name="id">
                                    <div class="form-group">
                                        <div class="col-lg-offset-2 col-lg-10">
                                            <button class="btn btn-primary ajax-post" type="submit" target-form="form-edit-auth">确定</button>
                                        </div>
                                    </div>
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
                                <th><input type="checkbox" class="i-checks" name="input[]"></th>
                                <th>用户组</th>
                                <th>描述</th>
                                <th>授权</th>
                                <th>状态</th>
                                <th>操作</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php if(is_array($_list) || $_list instanceof \think\Collection): $i = 0; $__LIST__ = $_list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?>
                            <tr>
                                <td><input type="checkbox" class="i-checks" name="input[]"></td>
                                <td><?php echo $vo['title']; ?></td>
                                <td><?php echo mb_strimwidth($vo['description'],0,60,"...","utf-8"); ?></td>
                                <td>
                                    <a href="<?php echo Url('Auth/access?group_name='.$vo['title'].'&group_id='.$vo['id']); ?>" >访问授权</a>
                                    <!--<a href="<?php echo Url('Auth/user?group_name='.$vo['title'].'&group_id='.$vo['id']); ?>" >成员授权</a>-->
                                </td>
                                <td><?php echo $vo['status_text']; ?></td>
                                <td>
                                    <?php if($vo['status'] == '1'): ?>
                                    <a href="<?php echo Url('Auth/changeStatus?method=forbidGroup&id='.$vo['id']); ?>" class="ajax-get">禁用</a>
                                    <?php else: ?>
                                    <a href="<?php echo Url('Auth/changeStatus?method=resumeGroup&id='.$vo['id']); ?>" class="ajax-get">启用</a>
                                    <?php endif; ?>
                                    <a data-toggle="modal" href="#modal-form-4" class="" onclick="editAuth('<?php echo $vo['title']; ?>','<?php echo $vo['description']; ?>','<?php echo $vo['id']; ?>')">编辑</a>
                                    <a href="<?php echo Url('Auth/changeStatus?method=deleteGroup&id='.$vo['id']); ?>" class="confirm ajax-get">删除</a>
                                </td>
                            </tr>
                            <?php endforeach; endif; else: echo "" ;endif; ?>
                            </tbody>
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


<!-- Peity -->
<script src="/admin/js/demo/peity-demo.js"></script>
<script src="/admin/js/plugins/peity/jquery.peity.min.js"></script>

<!-- iCheck -->
<script src="/admin/js/plugins/iCheck/icheck.min.js"></script>

<!-- Sweet alert -->
<script src="/admin/js/plugins/sweetalert/sweetalert.min.js"></script>

<script>
    $(document).ready(function(){
        $('.i-checks').iCheck({
            checkboxClass: 'icheckbox_square-green',
            radioClass: 'iradio_square-green'
        });
    });
    function editAuth(title,desc,id){
        var authForm = $("#modal-form-4");
        authForm.find("input[name='title']").val(title);
        authForm.find("textarea[name='description']").val(desc);
        authForm.find("input[name='id']").val(id);
    }
</script>

</body>
</html>
