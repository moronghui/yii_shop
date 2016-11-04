<?php
	use yii\bootstrap\ActiveForm;
	use yii\helpers\Html;
?>
<main id="authentication" class="inner-bottom-md">
	<div class="container">
		<div class="row">
			
			<div class="col-md-6">
				<section class="section sign-in inner-right-xs">
					<h2 class="bordered">登录</h2>
					<p>欢迎您回来，请您输入您的账户名密码</p>

					<div class="social-auth-buttons">
						<div class="row">
							<div class="col-md-6">
								<button class="btn-block btn-lg btn btn-facebook"><i class="fa fa-qq"></i> 使用QQ账号登录</button>
							</div>
							<div class="col-md-6">
								<button class="btn-block btn-lg btn btn-twitter"><i class="fa fa-weibo"></i> 使用新浪微博账号登录</button>
							</div>
						</div>
					</div>

					<?php 
						if (Yii::$app->session->hasFlash('infologin')) {
							echo Yii::$app->session->getFlash('infologin');
						}
					?>

					<?php 
						$form=ActiveForm::begin([
							'fieldConfig'=>[
								'template'=>'<div>{label}{input}</div>{error}'
							],
							'options'=>[
								'class'=>'login-form cf-style-1',
								'role' => 'form'
							],
							'action'=>['member/auth']
						]);
					?>
					<?php 
						echo $form->field($model,'loginname')->textInput(['class'=>'le-input']);
						echo $form->field($model,'userpass')->passwordInput(['class'=>'le-input']);
					?>
					<div class="field-row clearfix">
                    	<span class="pull-left">
                    		<?php 
                    			echo $form->field($model,'rememberMe')->checkbox([
                    				'id'=>'remember-me',
                    				'template'=>'<div class="remember">{input}<label for="remember-me">记住我</label></div>',
                    			]);
                    		?>
                    	</span>
                    	<span class="pull-right">
                    		<a href="<?php echo yii\helpers\Url::to(['admin/public/seekpassword']) ?>" class="content-color bold">忘记密码 ?</a>
                    	</span>
                    </div>
                    <?php 
						echo Html::submitButton('安全登录',['class'=>'le-button huge']);
					?>
					<?php
						ActiveForm::end();
					?>

				</section><!-- /.sign-in -->
			</div><!-- /.col -->

			<div class="col-md-6">
				<section class="section register inner-left-xs">
					<h2 class="bordered">新建账户</h2>
					<p>创建一个属于你自己的账户</p>

					<?php 
						if (Yii::$app->session->hasFlash('info')) {
							echo Yii::$app->session->getFlash('info');
						}
					?>
					<?php 
						$form=ActiveForm::begin([
							'fieldConfig'=>[
								'template'=>'<div>{label}{input}</div>{error}'
							],
							'action'=>['member/reg'],
							'options'=>[
								'class'=>'register-form cf-style-1',
								'role' => 'form'
							],
						]);
					?>
					<?php 
						echo $form->field($model,'useremail')->textInput(['class'=>'le-input']);
					?>
					<?php 
						echo Html::submitButton('注册',['class'=>'le-button huge']);
					?>
					<?php ActiveForm::end(); ?>


					<h2 class="semi-bold">加入我们您将会享受到前所未有的购物体验 :</h2>

					<ul class="list-unstyled list-benefits">
						<li><i class="fa fa-check primary-color"></i> 快捷的购物体验</li>
						<li><i class="fa fa-check primary-color"></i> 便捷的下单方式</li>
						<li><i class="fa fa-check primary-color"></i> 更加低廉的商品</li>
					</ul>

				</section><!-- /.register -->

			</div><!-- /.col -->

		</div><!-- /.row -->
	</div><!-- /.container -->
</main><!-- /.authentication -->

