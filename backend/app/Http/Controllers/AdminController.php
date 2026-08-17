<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;

class AdminController extends Controller
{
public function admin_login(Request $request){
$result = DB::select('select * from admin where username=? ', array($request->username));
if(count($result)>0){
    foreach($result as $r){
        if(password_verify($request->password,$r->password)){
            return 'success';
        }else{
            return 'fail';
        }
    }
}else{
    return 'fail';
}

}

public function personal_accounts(){
$result = DB::select('select * from Users where userType=? order by created desc ',array('0'));
return $result;
}

public function business_accounts(){
$result = DB::select('select * from Users where userType=? order by created desc ',array('1'));
return $result;
}

public function account_details(Request $request){
$result = DB::table('Users')
->where('id','=',$request->user_id)
->get();
return $result;
}

public function change_account_password(Request $request){
$password = bcrypt($request->password);
DB::update('update Users set password=? where id=?',array($password,$request->id));
return 'success';
}

public function delete_account(Request $request){
DB::delete('delete from Users where id=?', array($request->id));
DB::delete('delete from Accounts where userId=?', array($request->id));

return 'success';
}

public function get_shop(Request $request){
$result = DB::select('select * from Accounts where userId=? ', array($request->user_id));
return $result;
}

public function get_retailers(){
$result = DB::table('Accounts')
->where('accountType','=','Retailer')
->get();
return $result;
}

public function get_distributors(){
$result = DB::table('Accounts')
->where('accountType','=','Distributor')
->get();
return $result;
}

public function get_manufacturers(){
$result = DB::table('Accounts')
->where('accountType','=','Manufacturer')
->get();
return $result;
}

public function get_salons(){
$result = DB::table('Accounts')
->where('accountType','=','Salon')
->get();
return $result;
}


  public function loan_applications(){
$result = DB::select('select Loans.amount as amount, Loans.dueDate as due_date, Loans.createdAt as created, Loans.paid as paid, Loans.approved as approved, Loans.id as id, Users.fullName as full_name, Users.email as email, Users.phoneNumber as phone, Users.id as user_id from Loans left join Users on Loans.userId=Users.id');
return $result;
}


public function approve_loan(Request $request){

    if($request->status==0){
        $status = 1;
    }else{
        $status = 0;
    }

    DB::update('update Loans set approved=? where id=? ', array(intval($status),$request->id) );

    //update balances

    $data = DB::select('select * from Users where email=? ', array('tysonosore1@gmail.com') );


        #get loanee balance
        $result = DB::select('select * from Users where id=? ', array($request->user_id) );
        foreach($result as $r){
          $loanee_balance = $r->balance;
          $new_loanee_balance = $loanee_balance+$request->amount;
        }

         
          #get admin balance
          foreach($data as $d){
            $admin_balance = $d->balance;
            $new_admin_balance = $admin_balance-$request->amount;
          }
          #update records
          DB::update('update Users set balance=? where id=? ', array($new_loanee_balance,$request->user_id));
          DB::update('update Users set balance=? where email=? ', array($new_admin_balance,'tysonosore1@gmail.com'));

    return $status;
}





}
