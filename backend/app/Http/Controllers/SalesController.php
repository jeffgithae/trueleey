<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;

class SalesController extends Controller
{
  public function retailer_sales(Request $request){
    $result = DB::table('shopping_orders')
    ->join('Users','Users.id','shopping_orders.buyer')
    ->select(DB::raw('shopping_orders.order_id as order_id, shopping_orders.total_amount as total_amount, shopping_orders.date_bought as date_bought, Users.fullName as full_name'))
    ->where('retailer','=',$request->business_id)
    ->get();
    return $result;
  }

   public function retailer_website_orders(Request $request){
    $result = DB::table('shopping_orders')
    ->join('Users','Users.id','shopping_orders.buyer')
    ->select(DB::raw('shopping_orders.order_id as order_id, shopping_orders.total_amount as total_amount, shopping_orders.date_bought as date_bought, Users.fullName as full_name, shopping_orders.buyer as buyer'))
    ->where('retailer','=',$request->business_id)
    ->where('website_order','=',1)
    ->get();
    return $result;
  }

  public function pay_order(Request $request){

    #get receiver id from supplier id
    $supplier = DB::select('select * from Accounts where id=? ', array($request->supplier_id) );
    foreach($supplier as $s){
      $receiver = $s->userId;
    }

    #get receiver balance
    $result = DB::select('select * from Users where id=? ',array($receiver));
    foreach($result as $r){
      $receiver_new_balance = $r->balance + $request->amount;
    }


    #get sender balance
    $result_1 = DB::select('select * from Users where id=? ',array($request->sender));
    foreach($result_1 as $r_1){
      $sender_new_balance = $r_1->balance - $request->amount;
    }

    if($request->amount > $sender_new_balance ){
      return 'You have insufficient balance';
    }else{
      DB::update('update Users set balance=? where id=? ', array($receiver_new_balance,$receiver) );
      DB::update('update Users set balance=? where id=? ', array($sender_new_balance,$request->sender) );
      DB::insert('insert into orderPayments(orderId,paidBy,paidTo,amount,comment) values(?,?,?,?,?) ', array($request->order_id, $request->sender,$receiver, $request->amount, 'Paid for order') );
      DB::update('update Orders set paid=? where id=? ', array('Paid',$request->order_id) );
      return 'Order paid';
    }

  }




  public function pay_retailer_order(Request $request){

    #get receiver id from supplier id
    $supplier = DB::select('select * from Accounts where id=? ', array($request->supplier_id) );
    foreach($supplier as $s){
      $receiver = $s->userId;
    }

    #get receiver balance
    $result = DB::select('select * from Users where id=? ',array($receiver));
    foreach($result as $r){
      $receiver_new_balance = $r->balance + $request->amount;
    }


    #get sender balance
    $result_1 = DB::select('select * from Users where id=? ',array($request->sender));
    foreach($result_1 as $r_1){
      $sender_new_balance = $r_1->balance - $request->amount;
    }

    if($request->amount > $sender_new_balance ){
      return 'You have insufficient balance';
    }else{
      DB::update('update Users set balance=? where id=? ', array($receiver_new_balance,$receiver) );
      DB::update('update Users set balance=? where id=? ', array($sender_new_balance,$request->sender) );
      DB::insert('insert into orderPayments(orderId,paidBy,paidTo,amount,comment) values(?,?,?,?,?) ', array($request->order_id, $request->sender,$receiver, $request->amount, 'Paid for order') );
      DB::update('update RetailerOrders set paid=? where id=? ', array('Paid',$request->order_id) );
      return 'Order paid';
    }

  }

  public function mark_batch_received(Request $request){
    DB::update('update assigned_batches set received=? where batch_code=? ', array('Yes',$request->batch_code) );
    DB::update('update Orders set status=? where id=? ', array('Dispatched',$request->order_id) );
    return 'success';
  }

  public function mark_box_received(Request $request){
    DB::update('update RetailerBoxes set received=? where boxQrCode=? ', array('Yes',$request->box_code) );
    return 'success';
  }

  public function scan_box(Request $request){

    $result = DB::select('select * from RetailerBoxes where orderId=? and retailer=? and boxQrCode=? ', array($request->order_id, $request->business_id, $request->box_code) );
    if(count($result)>0){
      DB::update('update RetailerBoxes set received=? where boxQrCode=?', array('Yes',$request->box_code));
      return 'Box marked as received';
    }else{
    return 'Box does not exist in this order!';
    }

  }


  public function scan_batch(Request $request){

    $result = DB::select('select * from assigned_batches where order_id=? and receiver=? and batch_code=? ', array($request->order_id, $request->business_id, $request->batch_code) );
    if(count($result)>0){
      DB::update('update assigned_batches set received=? where batch_code=?', array('Yes',$request->batch_code));
      return 'Batch marked as received!';
    }else{
    return 'Batch does not exist in this order!';
    }

  }


  public function search_business(Request $request){
    $search_text = $request->search_text;
    $result = DB::table('Accounts')
                ->where('name', 'like', '%'.$search_text.'%')
                ->orWhere('location', 'like', '%'.$search_text.'%')
                ->get();
    return $result;
  }

  public function order_details(Request $request){
    $result = DB::table('RetailerBoxesProducts')
->join('RetailerProducts','RetailerBoxesProducts.productId','=','RetailerProducts.productId')
->join('Users','RetailerBoxesProducts.buyer','=','Users.id')
->select(DB::raw('RetailerProducts.productName as product_name, RetailerProducts.sellingPrice as selling_price, RetailerBoxesProducts.buyingPrice as buying_price, RetailerBoxesProducts.date_bought as date_bought, RetailerBoxesProducts.shopping_order_id as order_id, Users.fullName as full_name, Users.email as email'))
->where('RetailerBoxesProducts.retailer','=',$request->business_id)
->where('RetailerBoxesProducts.shopping_order_id','=',$request->order_id)
->where('RetailerBoxesProducts.bought','=','Yes')
->get();
return $result;
  }


public function my_loans(Request $request){
  $result = DB::select('select * from Loans where userId=? order by createdAt desc ',array($request->user_id) );
  return $result;
}


public function apply_loan(Request $request){

  $result = DB::select('select * from Loans where userId=? and paid=? ', array($request->user_id,0) );
  if(count($result)>0){
    return 'You have unpaid loan(s).Pay first to request for next loan.';
  }else{
    $today = date('Y-m-d');
    $date=date_create($today);
    date_add($date,date_interval_create_from_date_string("40 days"));
    $due_date=date_format($date,"Y-m-d");
    DB::insert('insert into Loans(userId,amount,dueDate) values(?,?,?) ', array($request->user_id,$request->amount,$due_date));
    return 'Loan application successful.';
  }

}




 public function pay_loan(Request $request){

      $data = DB::select('select * from Users where email=? ', array('tysonosore1@gmail.com') );


        #get sender balance
        $result = DB::select('select * from Users where id=? ', array($request->user_id) );
        foreach($result as $r){
          $sender_balance = $r->balance;
          $new_sender_balance = $sender_balance-$request->amount;
        }

        if($request->amount>$sender_balance){
          return 'You have insufficient funds in your account';
        }else {
          #get receiver balance
          foreach($data as $d){
            $receiver_balance = $d->balance;
            $new_receiver_balance = $receiver_balance+$request->amount;
          }
          #update records
          DB::update('update Users set balance=? where id=? ', array($new_sender_balance,$request->user_id));
          DB::update('update Users set balance=? where email=? ', array($new_receiver_balance,'tysonosore1@gmail.com'));

          #add transaction to table
          DB::insert('insert into loan_payments(user_id,amount) values(?,?) ', array($request->user_id, $request->amount) );
          #mark loan as paid 
          DB::update('update Loans set paid=? where id=?', array(1, $request->loan_id) );
          return 'Success.Loan paid successfully.';

        }


     

    }



    public function website_order_details(Request $request){
      $result = DB::select('select * from website_orders_items where order_id=? ', array($request->order_id) );
      return $result;
    }






}
