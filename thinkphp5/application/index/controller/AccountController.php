<?php
namespace app\index\controller;
use think\Controller;
use think\Db;
use app\common\model\Account;
use think\Request;

class AccountController extends Controller{
 
    public function index()
    {
        try {
             // 获取查询信息
             $name = Request::instance()->get('name');
             echo $name;

            $pageSize = 3; // 每页显示5条数据

            // 实例化Account
            $Account = new Account; 

             // 定制查询信息
             if (!empty($name)) {
                $Account->where('name', 'like', '%' . $name . '%');
            }


            // 调用分页
            $account = $Account->paginate($pageSize);

            // 向V层传数据
            $this->assign('Account', $account);

            // 取回打包后的数据
            $htmls = $this->fetch();

            // 将数据返回给用户
            return $htmls;

        // 获取到ThinkPHP的内置异常时，直接向上抛出，交给ThinkPHP处理
        } catch (\think\Exception\HttpResponseException $e) {
            throw $e;

        // 获取到正常的异常时，输出异常
        } catch (\Exception $e) {
            return $e->getMessage();
        } 

    }


    public function add(){
        return $this->fetch();
        
    }


    public function edit()
    {
        // 获取传入ID
        $id = Request::instance()->param('id/d');

        // 在Income表模型中获取当前记录
        $Account = Account::get($id);

        // 将数据传给V层
        $this->assign('Account', $Account);

        // 获取封装好的V层内容
        $htmls = $this->fetch();

        // 将封装好的V层内容返回给用户
        return $htmls;
    }


    
    public function insert(){
        $postData = $this->request->post();//接受传入的数据
        
        $Account = new Account();//空对象
       
        
        $Account->name = $postData['name'];
        $Account->create_time = $postData['create_time'];
   

    //$Account->create = 5613
    $Account->save();
    // 反馈结果
    return $this->success('添加成功',url('account_controller/index'));
    }


    public function delete()
    {
        // 获取pathinfo传入的ID值.
        $id = Request::instance()->param('id/d'); // “/d”表示将数值转化为“整形”

        if (is_null($id) || 0 === $id) {
            return $this->error('未获取到ID信息');
        }

        // 获取要删除的对象
        $Account = Account::get($id);

        // 要删除的对象不存在
        if (is_null($Account)) {
            return $this->error('不存在id为' . $id . '的类型，删除失败');
        }

        // 删除对象
        if (!$Account->delete()) {
            return $this->error('删除失败:' . $Account->getError());
        }

        // 进行跳转
        return $this->success('删除成功', url('index'));
    }

    public function update()
    {
        // 接收数据
        $income = Request::instance()->post();

        // 将数据存入Income表
        $Account = new Account();

        // 依据状态定制提示信息
        if (false === $Account->validate(true)->isUpdate(true)->save($income)) {
            return $this->error('更新失败' . $Account->getError());
        }

        return $this->success('操作成功', url('index'));
    }
}