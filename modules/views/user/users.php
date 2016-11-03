<!-- main container -->
<div class="content">
    <div class="container-fluid">
        <div id="pad-wrapper" class="users-list">
            <div class="row-fluid header">
                <h3>会员列表</h3>
                <div class="span10 pull-right">
                    <a href="<?php echo yii\helpers\Url::to(['user/reg']) ?>" class="btn-flat success pull-right">
                        <span>&#43;</span>添加新用户</a></div>
            </div>
            <!-- Users table -->
            <div class="row-fluid table">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th class="span3 sortable">
                                <span class="line"></span>用户名</th>
                            <th class="span3 sortable">
                                <span class="line"></span>真实姓名</th>
                            <th class="span2 sortable">
                                <span class="line"></span>昵称</th>
                            <th class="span3 sortable">
                                <span class="line"></span>性别</th>
                            <th class="span3 sortable">
                                <span class="line"></span>年龄</th>
                            <th class="span3 sortable">
                                <span class="line"></span>生日</th>
                            <th class="span3 sortable align-right">
                                <span class="line"></span>操作</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($users as $user) { ?>

                        <!-- row -->
                        <tr class="first">
                            <td>
                                <img src="assets/admin/img/contact-img.png" class="img-circle avatar hidden-phone" />
                                <a href="#" class="name"><?php echo $user->username  ?></a>
                                <span class="subtext"></span>
                            </td>
                            <td><?php echo isset($user->profile->truename) ? $user->profile->truename : '未填写' ?></td>
                            <td><?php echo isset($user->profile->nickname) ? $user->profile->nickname : '未填写' ?></td>
                            <td>
                                <?php
                                    if(isset($user->profile->truename))
                                    {
                                        switch ($user->profile->sex) {
                                            case '0':
                                                echo '男';
                                                break;
                                            case '1':
                                                echo '女';
                                                break;                                            
                                            case '2':
                                                echo '保密';
                                                break;
                                        }
                                    }
                                    else{
                                        echo '未填写' ;
                                    }

                                ?>
                            </td>
                            <td><?php echo isset($user->profile->age) ? $user->profile->age : '未填写' ?></td>
                            <td><?php echo isset($user->profile->birthday) ? $user->profile->birthday : '未填写' ?></td>
                            <td class="align-right">
                                <a onclick="return confirm('确定删除？');" href="<?php echo Yii::$app->urlManager->createAbsoluteUrl(['admin/user/del','id'=>$user->userid]); ?>">删除</a></td>
                        </tr>
                        <?php
                        } ?>
                    </tbody>
                </table>
                <div class="pagination pull-right">
                <?php echo yii\widgets\LinkPager::widget(['pagination'=>$pager,'prevPageLabel'=>'&#8249','nextPageLabel'=>'&#8250']); ?>
            </div>
            </div>
            <div class="pagination pull-right"></div>
            <!-- end users table --></div>
    </div>
</div>
<!-- end main container -->
