<?php

Route::model('feature', App\Feature::class);
Route::model('role', App\Role::class);
Route::model('area', App\Area::class);
Route::model('attachment', App\Attachment::class);
Route::model('company', App\Company::class);
Route::model('delivery_mode', App\DeliveryMode::class);
Route::model('user', App\User::class);
Route::model('category', App\Category::class);
Route::model('goods', App\Goods::class);
Route::model('rule', App\AssignRule::class);
Route::model('basket', App\Basket::class);
Route::model('demand', App\Demand::class);
Route::model('bid', App\Bid::class);
Route::model('offer', App\Offer::class);
Route::model('contract', App\Contract::class);
Route::model('sms', App\SmsTemplate::class);
Route::model('enquiry', App\Enquiry::class);
Route::model('offer_basket', App\OfferBasket::class);
Route::model('offer_information', App\OfferInformation::class);

Route::get('/', ['middleware' => 'auth', 'as'=>'home', function () {
    return redirect()->route('dashboard.bootstrap');
}]);

post('attachment/upload', [
    'as'         => 'attachment.upload',
    'uses'       => 'Attachment\AttachmentController@upload'
]);

get('attachment/download/{attachment}', [
    'as'         => 'attachment.download',
    'uses'       => 'Attachment\AttachmentController@download'
]);

get('/login', [
    'as'         => 'auth.login',
    'middleware' => 'guest',
    'uses'       => 'Auth\AuthController@login'
]);

post('/login', [
    'as'         => 'auth.login',
    'middleware' => 'guest',
    'uses'       => 'Auth\AuthController@auth'
]);

get('/logout', [
    'as'         => 'auth.logout',
    'middleware' => 'auth',
    'uses'       => 'Auth\AuthController@logout'
]);

get('/datetime', ['as' => 'system.datetime', function() {
    return date('Y-m-d H:i:s');
}]);

post('/password', [
    'middleware' => ['auth', 'permission'],
    'as'         => 'auth.password',
    'uses'       => 'Auth\AuthController@password'
]);

get('/auth/password/reset', [
    'as'         => 'auth.password.reset',
    'uses'       => 'Auth\AuthController@resetPassword'
]);

post('/auth/password/reset', [
    'as'         => 'auth.password.reset',
    'uses'       => 'Auth\AuthController@resetPasswordHandler'
]);

Route::group(['prefix' => 'dashboard', 'middleware' => ['auth', 'permission']], function() {

    get('/', [
        'as'   => 'dashboard.bootstrap',
        'uses' => 'Dashboard\DashboardController@bootstrap'
    ]);

    get('/welcome', [
        'as'   => 'dashboard.welcome',
        'uses' => 'Dashboard\DashboardController@welcome'
    ]);

    get('/agreement', [
        'as'   => 'dashboard.agreement',
        'uses' => 'Dashboard\DashboardController@showAgreement'
    ]);

    post('/agreement', [
        'as'   => 'dashboard.agreement',
        'uses' => 'Dashboard\DashboardController@agreement'
    ]);
});

Route::group(['prefix' => 'feature', 'middleware' => ['auth', 'permission']], function() {
    get('/', [
        'as'         => 'feature.manage',
        'uses'       => 'Feature\FeatureController@manage'
    ]);

    get('/add', [
        'as'         => 'feature.add',
        'uses'       => 'Feature\FeatureController@add'
    ]);

    post('/add', [
        'as'         => 'feature.add',
        'uses'       => 'Feature\FeatureController@create'
    ]);

    get('/edit/{feature}', [
        'as'         => 'feature.edit',
        'uses'       => 'Feature\FeatureController@edit'
    ]);

    post('/edit/{feature}', [
        'as'         => 'feature.edit',
        'uses'       => 'Feature\FeatureController@update'
    ]);

    get('/delete/{feature}', [
        'as'         => 'feature.delete',
        'uses'       => 'Feature\FeatureController@delete'
    ]);
});

Route::group(['prefix' => 'role', 'middleware' => ['auth', 'permission']], function() {
    get('/', [
        'as'         => 'role.manage',
        'uses'       => 'Role\RoleController@manage'
    ]);

    get('/add', [
        'as'         => 'role.add',
        'uses'       => 'Role\RoleController@add'
    ]);

    post('/add', [
        'as'         => 'role.add',
        'uses'       => 'Role\RoleController@create'
    ]);

    get('/edit/{role}', [
        'as'         => 'role.edit',
        'uses'       => 'Role\RoleController@edit'
    ]);

    post('/edit/{role}', [
        'as'         => 'role.edit',
        'uses'       => 'Role\RoleController@update'
    ]);

    get('/delete/{role}', [
        'as'         => 'role.delete',
        'uses'       => 'Role\RoleController@delete'
    ]);

    get('/permission/{role}', [
        'as'         => 'role.permission',
        'uses'       => 'Role\RoleController@permission'
    ]);

    post('/permission/{role}', [
        'as'         => 'role.permission',
        'uses'       => 'Role\RoleController@savePermission'
    ]);
});

Route::group(['prefix' => 'area', 'middleware' => ['auth', 'permission']], function() {
    get('/', [
        'as' => 'area.manage',
        'uses' => 'Area\AreaController@manage'
    ]);

    get('/add', [
        'as'         => 'area.add',
        'uses'       => 'Area\AreaController@add'
    ]);

    post('/add', [
        'as'         => 'area.add',
        'uses'       => 'Area\AreaController@create'
    ]);

    get('/edit/{area}', [
        'as'         => 'area.edit',
        'uses'       => 'Area\AreaController@edit'
    ]);

    post('/edit/{area}', [
        'as'         => 'area.edit',
        'uses'       => 'Area\AreaController@update'
    ]);

    get('/delete/{area}', [
        'as'         => 'area.delete',
        'uses'       => 'Area\AreaController@delete'
    ]);
});

Route::group(['prefix' => 'company', 'middleware' => ['auth', 'permission']], function() {
    get('/', [
        'as' => 'company.manage',
        'uses' => 'Company\CompanyController@manage'
    ]);

    get('/addition', [
        'as' => 'company.addition.index',
        'uses' => 'Company\AdditionController@index'
    ]);

    get('/addition/add', [
        'as' => 'company.addition.add',
        'uses' => 'Company\AdditionController@add'
    ]);

    post('/addition/add', [
        'as' => 'company.addition.add',
        'uses' => 'Company\AdditionController@append'
    ]);

    get('/addition/edit/{addition_id}', [
        'as' => 'company.addition.edit',
        'uses' => 'Company\AdditionController@edit'
    ]);

    post('/addition/edit/{addition_id}', [
        'as' => 'company.addition.edit',
        'uses' => 'Company\AdditionController@save'
    ]);

    get('/addition/delete/{addition_id}', [
        'as' => 'company.addition.delete',
        'uses' => 'Company\AdditionController@delete'
    ]);

    get('/add', [
        'as' => 'company.add',
        'uses' => 'Company\CompanyController@add'
    ]);

    post('/add', [
        'as' => 'company.add',
        'uses' => 'Company\CompanyController@create'
    ]);

    get('/edit/{company}', [
        'as' => 'company.edit',
        'uses' => 'Company\CompanyController@edit'
    ]);

    post('/edit/{company}', [
        'as' => 'company.edit',
        'uses' => 'Company\CompanyController@update'
    ]);

    get('/delete/{company}', [
        'as' => 'company.delete',
        'uses' => 'Company\CompanyController@delete'
    ]);

    get('/information', [
        'as' => 'company.information',
        'uses' => 'Company\CompanyController@information'
    ]);

    post('/information', [
        'as' => 'company.information',
        'uses' => 'Company\CompanyController@saveInformation'
    ]);

    get('/delivery_mode', [
        'as' => 'company.delivery_mode.manage',
        'uses' => 'Company\DeliveryModeController@manage'
    ]);

    get('/delivery_mode/add', [
        'as' => 'company.delivery_mode.add',
        'uses' => 'Company\DeliveryModeController@add'
    ]);

    post('/delivery_mode/add', [
        'as' => 'company.delivery_mode.add',
        'uses' => 'Company\DeliveryModeController@create'
    ]);

    get('/delivery_mode/edit/{delivery_mode}', [
        'as' => 'company.delivery_mode.edit',
        'uses' => 'Company\DeliveryModeController@edit'
    ]);

    post('/delivery_mode/edit/{delivery_mode}', [
        'as' => 'company.delivery_mode.edit',
        'uses' => 'Company\DeliveryModeController@update'
    ]);
});

Route::group(['prefix' => 'user', 'middleware' => ['auth', 'permission']], function() {
    get('/staff', [
        'as' => 'user.staff.manage',
        'uses' => 'User\StaffController@manage'
    ]);

    get('/staff/addition', [
        'as' => 'user.staff.addition.index',
        'uses' => 'User\StaffAdditionController@index'
    ]);

    get('/staff/addition/add', [
        'as' => 'user.staff.addition.add',
        'uses' => 'User\StaffAdditionController@add'
    ]);

    post('/staff/addition/add', [
        'as' => 'user.staff.addition.add',
        'uses' => 'User\StaffAdditionController@append'
    ]);

    get('/staff/addition/edit/{addition_id}', [
        'as' => 'user.staff.addition.edit',
        'uses' => 'User\StaffAdditionController@edit'
    ]);

    post('/staff/addition/edit/{addition_id}', [
        'as' => 'user.staff.addition.edit',
        'uses' => 'User\StaffAdditionController@save'
    ]);

    get('/staff/addition/delete/{addition_id}', [
        'as' => 'user.staff.addition.delete',
        'uses' => 'User\StaffAdditionController@delete'
    ]);

    get('/staff/add', [
        'as' => 'user.staff.add',
        'uses' => 'User\StaffController@add'
    ]);

    post('/staff/add', [
        'as' => 'user.staff.add',
        'uses' => 'User\StaffController@create'
    ]);

    get('/staff/edit/{user}', [
        'as' => 'user.staff.edit',
        'uses' => 'User\StaffController@edit'
    ]);

    post('/staff/edit/{user}', [
        'as' => 'user.staff.edit',
        'uses' => 'User\StaffController@update'
    ]);

    get('/staff/view/{user}', [
        'as' => 'user.staff.view',
        'uses' => 'User\StaffController@view'
    ]);

    get('/staff/disable/{user}', [
        'as' => 'user.staff.disable',
        'uses' => 'User\StaffController@disable'
    ]);

    get('/supplier', [
        'as' => 'user.supplier.manage',
        'uses' => 'User\SupplierController@manage'
    ]);

    get('/supplier/addition', [
        'as' => 'user.supplier.addition.index',
        'uses' => 'User\SupplierAdditionController@index'
    ]);

    get('/supplier/addition/add', [
        'as' => 'user.supplier.addition.add',
        'uses' => 'User\SupplierAdditionController@add'
    ]);

    post('/supplier/addition/add', [
        'as' => 'user.supplier.addition.add',
        'uses' => 'User\SupplierAdditionController@append'
    ]);

    get('/supplier/addition/edit/{addition_id}', [
        'as' => 'user.supplier.addition.edit',
        'uses' => 'User\SupplierAdditionController@edit'
    ]);

    post('/supplier/addition/edit/{addition_id}', [
        'as' => 'user.supplier.addition.edit',
        'uses' => 'User\SupplierAdditionController@save'
    ]);

    get('/supplier/addition/delete/{addition_id}', [
        'as' => 'user.supplier.addition.delete',
        'uses' => 'User\SupplierAdditionController@delete'
    ]);

    get('/supplier/add', [
        'as' => 'user.supplier.add',
        'uses' => 'User\SupplierController@add'
    ]);

    post('/supplier/add', [
        'as' => 'user.supplier.add',
        'uses' => 'User\SupplierController@create'
    ]);

    get('/supplier/edit/{user}', [
        'as' => 'user.supplier.edit',
        'uses' => 'User\SupplierController@edit'
    ]);

    post('/supplier/edit/{user}', [
        'as' => 'user.supplier.edit',
        'uses' => 'User\SupplierController@update'
    ]);

    get('/supplier/view/{user}', [
        'as' => 'user.supplier.view',
        'uses' => 'User\SupplierController@view'
    ]);

    get('/supplier/disable/{user}', [
        'as' => 'user.supplier.disable',
        'uses' => 'User\SupplierController@disable'
    ]);

    get('/phone/vcode', [
        'as'         => 'user.phone.vcode',
        'uses'       => 'User\PhoneController@vcode'
    ]);

    post('/phone/bind', [
        'as'         => 'user.phone.bind',
        'uses'       => 'User\PhoneController@bind'
    ]);

    get('/phone/unbind', [
        'as' => 'user.phone.unbind',
        'uses' => 'User\PhoneController@unbind'
    ]);
});

Route::group(['prefix' => 'goods', 'middleware' => ['auth', 'permission']], function() {
    get('/category', [
        'as' => 'goods.category.manage',
        'uses' => 'Goods\CategoryController@manage'
    ]);

    get('/category/add', [
        'as' => 'goods.category.add',
        'uses' => 'Goods\CategoryController@add'
    ]);

    post('/category/add', [
        'as' => 'goods.category.add',
        'uses' => 'Goods\CategoryController@create'
    ]);

    get('/category/edit/{category}', [
        'as' => 'goods.category.edit',
        'uses' => 'Goods\CategoryController@edit'
    ]);

    post('/category/edit/{category}', [
        'as' => 'goods.category.edit',
        'uses' => 'Goods\CategoryController@update'
    ]);

    get('/category/disable/{category}', [
        'as' => 'goods.category.disable',
        'uses' => 'Goods\CategoryController@disable'
    ]);

    get('/category/addition/{category}', [
        'as' => 'goods.category.addition.index',
        'uses' => 'Goods\CategoryAdditionController@index'
    ]);

    get('/category/addition/add/{category}', [
        'as' => 'goods.category.addition.add',
        'uses' => 'Goods\CategoryAdditionController@add'
    ]);

    post('/category/addition/add/{category}', [
        'as' => 'goods.category.addition.add',
        'uses' => 'Goods\CategoryAdditionController@append'
    ]);

    get('/category/addition/edit/{category}/{addition_id}', [
        'as' => 'goods.category.addition.edit',
        'uses' => 'Goods\CategoryAdditionController@edit'
    ]);

    post('/category/addition/edit/{category}/{addition_id}', [
        'as' => 'goods.category.addition.edit',
        'uses' => 'Goods\CategoryAdditionController@save'
    ]);

    get('/category/addition/delete/{category}/{addition_id}', [
        'as' => 'goods.category.addition.delete',
        'uses' => 'Goods\CategoryAdditionController@delete'
    ]);

    get('/', [
        'as' => 'goods.manage',
        'uses' => 'Goods\GoodsController@manage'
    ]);

    get('/add', [
        'as' => 'goods.add',
        'uses' => 'Goods\GoodsController@add'
    ]);

    post('/add', [
        'as' => 'goods.add',
        'uses' => 'Goods\GoodsController@create'
    ]);

    get('/view/{goods}', [
        'as' => 'goods.view',
        'uses' => 'Goods\GoodsController@view'
    ]);

    get('/edit/{goods}', [
        'as' => 'goods.edit',
        'uses' => 'Goods\GoodsController@edit'
    ]);

    post('/edit/{goods}', [
        'as' => 'goods.edit',
        'uses' => 'Goods\GoodsController@update'
    ]);

    get('/disable/{goods}', [
        'as' => 'goods.disable',
        'uses' => 'Goods\GoodsController@disable'
    ]);
});

Route::group(['prefix' => 'setting', 'middleware' => ['auth', 'permission']], function() {

    get('/check_flow', [
        'as' => 'setting.check_flow',
        'uses' => 'Setting\CheckFlowController@index'
    ]);

    post('/check_flow', [
        'as' => 'setting.check_flow',
        'uses' => 'Setting\CheckFlowController@save'
    ]);

    get('/offer_min', [
        'as' => 'setting.offer_min',
        'uses' => 'Setting\CheckFlowController@offerMinNum'
    ]);

    post('/offer_min', [
        'as' => 'setting.offer_min',
        'uses' => 'Setting\CheckFlowController@saveOfferMinNum'
    ]);

    get('/assign_rule', [
        'as' => 'setting.assign_rule',
        'uses' => 'Setting\AssignRuleController@manage'
    ]);

    get('/assign_rule/add', [
        'as' => 'setting.assign_rule.add',
        'uses' => 'Setting\AssignRuleController@add'
    ]);

    post('/assign_rule/add', [
        'as' => 'setting.assign_rule.add',
        'uses' => 'Setting\AssignRuleController@create'
    ]);

    get('/assign_rule/edit/{rule}', [
        'as' => 'setting.assign_rule.edit',
        'uses' => 'Setting\AssignRuleController@edit'
    ]);

    post('/assign_rule/edit/{rule}', [
        'as' => 'setting.assign_rule.edit',
        'uses' => 'Setting\AssignRuleController@update'
    ]);

    get('/assign_rule/delete/{rule}', [
        'as' => 'setting.assign_rule.delete',
        'uses' => 'Setting\AssignRuleController@delete'
    ]);
});

Route::group(['prefix' => 'demand', 'middleware' => ['auth', 'permission']], function() {

    get('/staff', [
        'as' => 'demand.staff.manage',
        'uses' => 'Demand\StaffController@manage'
    ]);

    get('/staff/add', [
        'as' => 'demand.staff.add',
        'uses' => 'Demand\StaffController@add'
    ]);

    post('/staff/add', [
        'as' => 'demand.staff.add',
        'uses' => 'Demand\StaffController@create'
    ]);

    get('/staff/view/{basket}', [
        'as' => 'demand.staff.view',
        'uses' => 'Demand\StaffController@view'
    ]);

    get('/staff/demand_list/{basket}', [
        'as' => 'demand.staff.demand_list',
        'uses' => 'Demand\StaffController@demandList'
    ]);

    get('/staff/append/{basket}', [
        'as' => 'demand.staff.append',
        'uses' => 'Demand\StaffController@append'
    ]);

    post('/staff/append/{basket}', [
        'as' => 'demand.staff.append',
        'uses' => 'Demand\StaffController@save'
    ]);

    get('/staff/edit/{demand}', [
        'as' => 'demand.staff.edit',
        'uses' => 'Demand\StaffController@edit'
    ]);

    post('/staff/edit/{demand}', [
        'as' => 'demand.staff.edit',
        'uses' => 'Demand\StaffController@update'
    ]);

    get('/staff/delete/{demand}', [
        'as' => 'demand.staff.delete',
        'uses' => 'Demand\StaffController@delete'
    ]);

    get('/check', [
        'as' => 'demand.check.index',
        'uses' => 'Demand\CheckController@index'
    ]);

    post('/check/edit/{demand}', [
        'as' => 'demand.check.edit',
        'uses' => 'Demand\CheckController@edit'
    ]);

    post('/check/modify/{demand}', [
        'as' => 'demand.check.modify',
        'uses' => 'Demand\CheckController@modify'
    ]);

    get('/check/view/{basket}', [
        'as' => 'demand.check.action',
        'uses' => 'Demand\CheckController@view'
    ]);

    post('/check/view/{basket}', [
        'as' => 'demand.check.action',
        'uses' => 'Demand\CheckController@action'
    ]);
});

Route::group(['prefix' => 'bid', 'middleware' => ['auth', 'permission']], function() {

    get('/demand', [
        'as' => 'bid.demand.index',
        'uses' => 'Bid\DemandController@index'
    ]);

    get('/view/{basket}', [
        'as' => 'bid.demand.view',
        'uses' => 'Bid\DemandController@view'
    ]);

    get('/generate/{basket}', [
        'as' => 'bid.demand.collect',
        'uses' => 'Bid\DemandController@collect'
    ]);

    post('/generate/{basket}', [
        'as' => 'bid.demand.collect',
        'uses' => 'Bid\DemandController@generate'
    ]);

    get('/cancel/{basket}', [
        'as' => 'bid.demand.cancel',
        'uses' => 'Bid\DemandController@cancel'
    ]);

    get('/edit/{basket}', [
        'as' => 'bid.demand.edit',
        'uses' => 'Bid\DemandController@edit'
    ]);

    post('/edit/{basket}', [
        'as' => 'bid.demand.edit',
        'uses' => 'Bid\DemandController@update'
    ]);

    get('/check', [
        'as' => 'bid.check.index',
        'uses' => 'Bid\CheckController@index'
    ]);

    get('/check/{basket}', [
        'as' => 'bid.check.view',
        'uses' => 'Bid\CheckController@view'
    ]);

    post('/check/{basket}', [
        'as' => 'bid.check.view',
        'uses' => 'Bid\CheckController@action'
    ]);

    get('/supplier', [
        'as' => 'bid.supplier.index',
        'uses' => 'Bid\SupplierController@index'
    ]);

    get('/supplier/pending', [
        'as' => 'bid.supplier.pending',
        'uses' => 'Bid\SupplierController@pending'
    ]);

    get('/supplier/done', [
        'as' => 'bid.supplier.done',
        'uses' => 'Bid\SupplierController@done'
    ]);

    get('/supplier/offer/{bid}', [
        'as' => 'bid.supplier.offer',
        'uses' => 'Bid\SupplierController@offer'
    ]);

    post('/supplier/offer/{bid}', [
        'as' => 'bid.supplier.offer',
        'uses' => 'Bid\SupplierController@saveOffer'
    ]);

    get('/supplier/view/{bid}', [
        'as' => 'bid.supplier.view',
        'uses' => 'Bid\SupplierController@view'
    ]);

    get('/company', [
        'as' => 'bid.company.index',
        'uses' => 'Bid\CompanyController@index'
    ]);

    get('/company/offer/{offer}', [
        'as' => 'bid.company.offer',
        'uses' => 'Bid\CompanyController@offer'
    ]);

    post('/company/offer/{offer}', [
        'as' => 'bid.company.offer',
        'uses' => 'Bid\CompanyController@generateContract'
    ]);

    get('/company/upload_contract/{offer}', [
        'as' => 'bid.company.upload_contract',
        'uses' => 'Bid\CompanyController@uploadContract'
    ]);

});

Route::group(['prefix' => 'contract', 'middleware' => ['auth', 'permission']], function() {

    get('/company', [
        'as' => 'contract.company.index',
        'uses' => 'Contract\CompanyController@index'
    ]);

    get('/company/view/{contract}', [
        'as' => 'contract.company.view',
        'uses' => 'Contract\CompanyController@view'
    ]);

    get('/company/edit/{contract}', [
        'as' => 'contract.company.edit',
        'uses' => 'Contract\CompanyController@edit'
    ]);

    post('/company/edit/{contract}', [
        'as' => 'contract.company.edit',
        'uses' => 'Contract\CompanyController@save'
    ]);

    get('/company/confirm/{contract}', [
        'as' => 'contract.company.confirm',
        'uses' => 'Contract\CompanyController@confirm'
    ]);

    get('/company/finish/{contract}', [
        'as' => 'contract.company.finish',
        'uses' => 'Contract\CompanyController@finish'
    ]);

    post('/company/grade/{contract}', [
        'as' => 'contract.company.grade',
        'uses' => 'Contract\CompanyController@grade'
    ]);

    post('/supplier/grade/{contract}', [
        'as' => 'contract.supplier.grade',
        'uses' => 'Contract\SupplierController@grade'
    ]);

    get('/supplier', [
        'as' => 'contract.supplier.index',
        'uses' => 'Contract\SupplierController@index'
    ]);

    get('/supplier/view/{contract}', [
        'as' => 'contract.supplier.view',
        'uses' => 'Contract\SupplierController@view'
    ]);

    post('/supplier/suggest/{contract}', [
        'as' => 'contract.supplier.suggest',
        'uses' => 'Contract\SupplierController@suggest'
    ]);

    post('/supplier/confirm/{contract}', [
        'as' => 'contract.supplier.confirm',
        'uses' => 'Contract\SupplierController@confirm'
    ]);

    get('/download/{contract}', [
        'as' => 'contract.download',
        'uses' => 'Contract\ContractController@download'
    ]);

    get('/attachment/{contract}', [
        'as' => 'contract.attachment',
        'uses' => 'Contract\CompanyController@attachment'
    ]);

    post('/attachment/{contract}', [
        'as' => 'contract.attachment',
        'uses' => 'Contract\CompanyController@saveAttachment'
    ]);

    get('/supplier/attachment/{contract}', [
        'as' => 'contract.supplier.attachment',
        'uses' => 'Contract\SupplierController@attachment'
    ]);

});

Route::group(['prefix' => 'statistics', 'middleware' => ['auth', 'permission']], function() {

    get('/supplier', [
        'as' => 'statistics.supplier',
        'uses' => 'Statistics\StatisticsController@supplier'
    ]);

    get('/company', [
        'as' => 'statistics.company',
        'uses' => 'Statistics\StatisticsController@company'
    ]);

    get('/grade', [
        'as' => 'statistics.grade',
        'uses' => 'Statistics\StatisticsController@grade'
    ]);

    get('/bid_count', [
        'as' => 'statistics.bid_count',
        'uses' => 'Statistics\StatisticsController@bid_count'
    ]);

    get('/bid_rate', [
        'as' => 'statistics.bid_rate',
        'uses' => 'Statistics\StatisticsController@bid_rate'
    ]);

    /*get('/test', [
        'as' => 'statistics.test',
        'uses' => 'Statistics\StatisticsController@test'
    ]);*/

});

Route::group(['prefix' => 'sms', 'middleware' => ['auth', 'permission']], function() {

    get('/', [
        'as' => 'sms.index',
        'uses' => 'Sms\SmsController@index'
    ]);

    get('/add', [
        'as' => 'sms.add',
        'uses' => 'Sms\SmsController@add'
    ]);

    post('/add', [
        'as' => 'sms.add',
        'uses' => 'Sms\SmsController@create'
    ]);

    get('/check/{sms}', [
        'as' => 'sms.check',
        'uses' => 'Sms\SmsController@check'
    ]);

    post('/check/{sms}', [
        'as' => 'sms.check',
        'uses' => 'Sms\SmsController@saveCheck'
    ]);

    get('/send/supplier/{sms}', [
        'as' => 'sms.send.supplier',
        'uses' => 'Sms\SmsController@showSendSupplierPage'
    ]);

    post('/send/supplier/{sms}', [
        'as' => 'sms.send.supplier',
        'uses' => 'Sms\SmsController@sendSupplier'
    ]);

    get('/send/staff/{sms}', [
        'as' => 'sms.send.staff',
        'uses' => 'Sms\SmsController@showSendStaffPage'
    ]);

    post('/send/staff/{sms}', [
        'as' => 'sms.send.staff',
        'uses' => 'Sms\SmsController@sendStaff'
    ]);

});

Route::group(['prefix' => 'enquiry', 'middleware' => ['auth', 'permission']], function() {

    get('/staff/index', [
        'as' => 'enquiry.staff.index',
        'uses' => 'Enquiry\StaffController@index'
    ]);

    get('/staff/add', [
        'as' => 'enquiry.staff.add',
        'uses' => 'Enquiry\StaffController@add'
    ]);

    post('/staff/add', [
        'as' => 'enquiry.staff.add',
        'uses' => 'Enquiry\StaffController@create'
    ]);

    get('/supplier/view/{enquiry}', [
        'as' => 'enquiry.staff.view',
        'uses' => 'Enquiry\StaffController@view'
    ]);

    get('/supplier/index', [
        'as' => 'enquiry.supplier.index',
        'uses' => 'Enquiry\SupplierController@index'
    ]);

    get('/supplier/reply/{enquiry}', [
        'as' => 'enquiry.supplier.view',
        'uses' => 'Enquiry\SupplierController@view'
    ]);

    post('/supplier/reply/{enquiry}', [
        'as' => 'enquiry.supplier.reply',
        'uses' => 'Enquiry\SupplierController@reply'
    ]);

});

Route::group(['prefix' => 'offer', 'middleware' => ['auth', 'permission']], function() {

    get('/information/index', [
        'as' => 'offer.information.index',
        'uses' => 'Offer\InformationController@index'
    ]);

    get('/information/create', [
        'as' => 'offer.information.create',
        'uses' => 'Offer\InformationController@create'
    ]);

    get('/information/list/{offer_basket}', [
        'as' => 'offer.information.list',
        'uses' => 'Offer\InformationController@informationList'
    ]);

    get('/information/append/{offer_basket}', [
        'as' => 'offer.information.append',
        'uses' => 'Offer\InformationController@append'
    ]);

    post('/information/append/{offer_basket}', [
        'as' => 'offer.information.append',
        'uses' => 'Offer\InformationController@saveAppend'
    ]);

    get('/information/edit/{offer_information}', [
        'as' => 'offer.information.edit',
        'uses' => 'Offer\InformationController@edit'
    ]);

    post('/information/edit/{offer_information}', [
        'as' => 'offer.information.edit',
        'uses' => 'Offer\InformationController@saveEdit'
    ]);

    get('/information/publish/{offer_information}', [
        'as' => 'offer.information.publish',
        'uses' => 'Offer\InformationController@publish'
    ]);

    get('/information/delete/{offer_information}', [
        'as' => 'offer.information.delete',
        'uses' => 'Offer\InformationController@delete'
    ]);

    get('/information/view/{offer_information}', [
        'as' => 'offer.information.view',
        'uses' => 'Offer\InformationController@view'
    ]);

    get('/information/company/index', [
        'as' => 'offer.information.company.index',
        'uses' => 'Offer\CompanyInformationController@index'
    ]);

    get('/information/company/list/{offer_basket}', [
        'as' => 'offer.information.company.list',
        'uses' => 'Offer\CompanyInformationController@informationList'
    ]);
});

