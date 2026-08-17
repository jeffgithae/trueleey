<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;

use App\Mail\NewOrder;
use Illuminate\Support\Facades\Mail;

class ProductsController extends Controller
{
    public function business_products(Request $request){
        $result = DB::select('select * from Products where ownerId=? ', array($request->business_id) );

        return $result;
    }

    public function search_product(Request $request){
      $search_text = $request->search_text;
      $result = DB::table('Products')
                  ->where('productName', 'like', '%'.$search_text.'%')
                  ->where('ownerId', '=', $request->business_id)
                  ->get();
      return $result;
    }

    public function distributor_orders(Request $request){
        $result = DB::select('select Accounts.name as distributor_name, Accounts.email as email, Accounts.location as distributor_location, Orders.productName as product_name, Orders.delivery_date as delivery_date, Orders.quantity as quantity, Orders.deliveryLocation as delivery_location, Orders.status as status, Orders.totalPrice as total_price, Orders.id as order_id from Accounts inner join Orders on Accounts.id=Orders.owner_id where Orders.supplier_id=? ', array($request->business_id) );

        return $result;
    }

 public function distributor_order_details(Request $request){
        $result = DB::select('select Accounts.name as distributor_name, Accounts.email as email, Accounts.location as distributor_location, Orders.productName as product_name, Orders.delivery_date as delivery_date, Orders.quantity as quantity, Orders.deliveryLocation as delivery_location, Orders.status as status, Orders.totalPrice as total_price, Orders.id as order_id, Orders.paid as paid, Orders.specifications as specifications, Orders.instructions as instructions, Orders.created as created, Orders.owner_id as owner_id, Orders.product_id as product_id, Orders.supplier_id as supplier_id from Accounts inner join Orders on Accounts.id=Orders.owner_id where Orders.id=? ', array($request->id) );

        return $result;
    }





    public function change_order_status(Request $request){
        DB::update('update Orders set status=? where id=?', array($request->status,$request->order_id) );
        return 'success';
    }


    public function delete_order(Request $request){
        $result = DB::select('select * from assigned_batches where order_id=? ', array($request->order_id) );

        if(count($result)>0){
            return 'Order has product batches.Delete batches first';
        }else{
            DB::update('delete from Orders where id=?', array($request->order_id) );
        return 'Order deleted';
        }


    }


      public function search_order(Request $request){
        $search_text = $request->search_text;

      $result = DB::table('shopping_orders')
      ->join('Users','Users.id','shopping_orders.buyer')
      ->select(DB::raw('shopping_orders.order_id as order_id, shopping_orders.total_amount as total_amount, shopping_orders.date_bought as date_bought, Users.fullName as full_name'))
      ->where('shopping_orders.order_id', 'like', '%'.$search_text.'%')
      ->where('shopping_orders.retailer','=',$request->business_id)
      ->get();

        return $result;
    }


       public function outgoing_orders(Request $request){
        $result = DB::select('select Accounts.name as distributor_name, Accounts.email as email, Accounts.location as distributor_location, Orders.productName as product_name, Orders.delivery_date as delivery_date, Orders.quantity as quantity, Orders.deliveryLocation as delivery_location, Orders.status as status, Orders.totalPrice as total_price, Orders.id as order_id, Orders.supplier_id as supplier_id from Accounts inner join Orders on Accounts.id=Orders.supplier_id where Orders.owner_id=? ', array($request->business_id) );

        return $result;
    }


    public function new_product(Request $request){
      $image=time().rand(100,10000).'.'.$request->image->extension();
      $req=$request->image->move('assets/product-images/', $image);

        DB::insert('insert into Products(productName,category,ownerId,price,description,dateManufactured,expiryDate,weight,shippingCostPerItem,productionCost,reorder_level,productPicture) values(?,?,?,?,?,?,?,?,?,?,?,?) ', array($request->name, $request->category, $request->owner, $request->price, $request->description, $request->date_manufactured, $request->expiry_date, $request->weight, $request->shipping_cost, $request->production_cost, $request->reorder_level, $image ) );

        return 'success';

    }

    public function product_categories(Request $request){
        $result = DB::select('select * from ProductCategories order by name asc');
        return $result;
    }


    public function business_batches(Request $request){
        $result = DB::select('select BatchProductDeals.id as batch_id, BatchProductDeals.numberOfProductCodes as number_of_product_codes, BatchProductDeals.batchQRCode as batch_qr_code, BatchProductDeals.batchName as batch_name, BatchProductDeals.boxes as boxes, BatchProductDeals.quantityPerBox as quantity_per_box, BatchProductDeals.assigned as assigned, BatchProductDeals.printed as printed, Products.productName as product_name from BatchProductDeals inner join Products on Products.id=BatchProductDeals.productId where BatchProductDeals.userId=?  ', array($request->business_id) );
        return $result;
    }


    public function add_batch_to_order(Request $request){

        DB::insert('insert into assigned_batches(order_id,batch_code,sender,receiver) values(?,?,?,?) ', array($request->order_id, $request->batch_code, $request->sender, $request->receiver) );
        DB::update('update BatchProductDeals set assigned=? where batchQRCode=? ', array(1,$request->batch_code) );

        #set product code as used on stock table
        $result = DB::select('select * from BoxProducts where batchCode=? ', array($request->batch_code) );
        if(count($result)>0){
          foreach($result as $r){
            $product_code = $r->productQrCode;
            DB::update('update stock set used=? where product_code=?',array('Yes',$product_code));
          }
        }
        DB::update('update Orders set status=? where id=? ', array(1,$request->order_id));
        return 'success';
    }

      public function batches_to_assign(Request $request){
        $result = DB::select('select BatchProductDeals.id as batch_id, BatchProductDeals.numberOfProductCodes as number_of_product_codes, BatchProductDeals.batchQRCode as batch_qr_code, BatchProductDeals.batchName as batch_name, BatchProductDeals.boxes as boxes, BatchProductDeals.quantityPerBox as quantity_per_box, BatchProductDeals.assigned as assigned, BatchProductDeals.printed as printed, Products.productName as product_name from BatchProductDeals inner join Products on Products.id=BatchProductDeals.productId where BatchProductDeals.userId=? and BatchProductDeals.productId=? and BatchProductDeals.assigned=?  ', array($request->business_id, $request->product_id,0) );
        return $result;
    }

    public function assigned_batches(Request $request){
        $result = DB::select('select assigned_batches.batch_code as batch_code,assigned_batches.received as received, BatchProductDeals.batchName as batch_name from assigned_batches inner join BatchProductDeals on assigned_batches.batch_code=BatchProductDeals.batchQRCode where assigned_batches.order_id=? ', array($request->order_id) );
        return $result;
    }


    public function delete_assigned_batch(Request $request){
        DB::update('update BatchProductDeals set assigned=? where batchQRCode=? ', array(0,$request->batch_code) );
        DB::delete('delete from assigned_batches where batch_code=?',array($request->batch_code));
        return 'success';
    }

    public function get_suppliers(Request $request){
        $result = DB::select('select * from Accounts order by name asc ');
        return $result;
    }

    public function new_supplier(Request $request){
        $result = DB::select('select * from my_suppliers where business_id=? and supplier_id=? ', array($request->business_id,$request->supplier_id) );
        if(count($result)>0){
            return 'Supplier already exists in your account';
        }else{
            DB::insert('insert into my_suppliers(business_id,supplier_id) values(?,?) ', array($request->business_id,$request->supplier_id) );
            return 'Supplier added to your list';
        }
    }


    public function my_suppliers(Request $request){
        $result = DB::select('select Accounts.name as name,Accounts.id as id, Accounts.email as email, Accounts.location as location, Accounts.accountType as account_type from Accounts inner join my_suppliers on Accounts.id=my_suppliers.supplier_id where my_suppliers.business_id=? ', array($request->business_id) );
        return $result;
    }


     public function get_products(Request $request){
        $result = DB::select('select * from Products where ownerId=? ', array($request->business_id) );

        return $result;
    }

    public function new_batch(Request $request){

      #before creating batch check stock
      $total_stock = 0;
      $generated_product_batches = 0;
      $available_stock = 0;
      $stock = DB::select('select * from ProductStock where product_id=? and account_id=? ', array($request->product_id,$request->business_id) );
      if(count($stock)>0){
        foreach($stock as $s){
          $total_stock += $s->amount;
        }
      }else{
        $total_stock = 0;
      }

      //get generated product batches
      $generated = DB::select('select * from BatchProductDeals where userId=? and productId=? ',array($request->business_id, $request->product_id));
       if(count($generated)>0){
          foreach($generated as $g){
            $generated_product_batches += $g->numberOfProductCodes;
          }
       }else{
         $generated_product_batches = 0;
       }

       $available_stock = $total_stock - $generated_product_batches;
       $product_codes = intval($request->boxes) * intval($request->quantity);

       if($product_codes>$available_stock){
         #stock is not enough

         return 'Batch cannot be generated.You have insufficient products in stock';
       }else{
         #stock is enough proceed

         #generate batch code

         $batch_code = md5(time());
         DB::insert('insert into BatchProductDeals(productId, userId, numberOfProductCodes, batchQRCode, batchName, boxes, quantityPerBox) values(?,?,?,?,?,?,?)', array($request->product_id, $request->business_id, $product_codes, $batch_code, $request->batch_name, $request->boxes, $request->quantity) );

         #generate boxes
         $counter = 0;
         while($counter<$request->boxes){
             $box_code = md5($counter.rand(100,100000).time());
             DB::insert('insert into Boxes(batchCode, boxQrCode, productQty, userId, productId) values(?,?,?,?,?) ', array($batch_code, $box_code, $request->quantity, $request->business_id, $request->product_id ) );

             #generate products
             $counter_2 = 0;
             while($counter_2<$request->quantity){
               //////
               $result = DB::select('select * from stock where used=? and business_id=? and product_id=? limit 1', array('No',$request->business_id,$request->product_id) );
               if(count($result)>0){
                 foreach($result as $r){
                   DB::insert('insert into BoxProducts(boxQrCode, productQrCode, productId, userId, batchCode) values(?,?,?,?,?) ', array($box_code, $r->product_code, $request->product_id, $request->business_id, $batch_code ) );
                   DB::update('update stock set used=? where id=?',array('Yes',$r->id));
                 }
               }
               /////
                 $counter_2 ++;
             }

             $counter ++;

         }
        return 'Batch added to list';
       }

    }



    public function supplier_products(Request $request){
        $result = DB::select('select * from Products where ownerId=? order by productName asc ', array($request->supplier_id));
        return $result;
    }

    public function create_order(Request $request){
        #get total qty
        $total_quantity = intval($request->boxes) * intval($request->quantity_per_box);
        #get total price for order
        $total_price = 0;
        $result = DB::select('select * from Products where id=? ', array($request->product_id));
        if(count($result)>0){
            foreach($result as $r){
                $total_price = intval($r->price) * intval($total_quantity);
                $product_name = $r->productName;
            }
        }

        #check if account type is distributor and has products on distributor_products
        if($request->account_type=='Distributor'){
          $data = DB::select('select * from DistributorProducts where distributor=? and productId=? ', array($request->business_id,$request->product_id) );
          if(count($data)==0){
            $message = 'For you to make this order you need to register the selected product on your products section';
          }else{
          #everything is fine proceed
            DB::insert('insert Orders(supplier_id, owner_id, delivery_date, quantity, product_id, specifications, instructions, deliveryLocation, totalPrice, boxes, quantity_per_box,productName) values(?,?,?,?,?,?,?,?,?,?,?,?) ', array($request->supplier_id, $request->business_id, $request->delivery_date, $total_quantity, $request->product_id, $request->specifications, $request->instructions, $request->delivery_location, $total_price, $request->boxes, $request->quantity_per_box,$product_name) );
            $message= 'Order created';
          }
        }


        #check if account type is retailer and has products on retailer_products
        if($request->account_type=='Retailer'){
          $data = DB::select('select * from RetailerProducts where retailer=? and productId=? ', array($request->business_id,$request->product_id) );
          if(count($data)==0){
            return 'For you to make this order you need to register the selected product on your products section';
          }else{
          #everything is fine proceed
            DB::insert('insert Orders(supplier_id, owner_id, delivery_date, quantity, product_id, specifications, instructions, deliveryLocation, totalPrice, boxes, quantity_per_box,productName) values(?,?,?,?,?,?,?,?,?,?,?,?) ', array($request->supplier_id, $request->business_id, $request->delivery_date, $total_quantity, $request->product_id, $request->specifications, $request->instructions, $request->delivery_location, $total_price, $request->boxes, $request->quantity_per_box,$product_name) );
            $message = 'Order created';
          }
        }

        #get business details and send emails
        #sender details
        $result_1 = DB::select('select * from Accounts where id=? ', array($request->business_id) );
        foreach($result_1 as $r){
          $sender_email = $r->email;
        }
        $details_1 = ['message'=>'Your order was submmitted successfully.Login to your account to check and keep track of your order'];
        #supplier details
        $result_2 = DB::select('select * from Accounts where id=? ', array($request->supplier_id) );
        foreach($result_2 as $r){
          $supplier_email = $r->email;
        }
        $details_2 = ['message'=>'You have a new order.Login in to your account to check and process the order'];
        Mail::to($sender_email)->send(new NewOrder($details_1));
        Mail::to($supplier_email)->send(new NewOrder($details_2));

        return $message;

    }


    public function supplier_details(Request $request){
        $result = DB::select('select * from Accounts where id=? ', array($request->supplier_id) );
        return $result;
    }

    public function  received_batches(Request $request){
        $result = DB::select('select assigned_batches.batch_code as batch_code,assigned_batches.received as received, assigned_batches.order_id as order_id, BatchProductDeals.batchName as batch_name, BatchProductDeals.boxes as boxes, BatchProductDeals.quantityPerBox as quantity_per_box, Products.productName as product_name, Products.price as price from assigned_batches inner join BatchProductDeals on assigned_batches.batch_code=BatchProductDeals.batchQRCode inner join Products on Products.id=BatchProductDeals.productId where assigned_batches.receiver=? ', array($request->business_id) );
        return $result;
    }


    public function  d_received_batches(Request $request){
        $result = DB::select('select assigned_batches.batch_code as batch_code,assigned_batches.order_id as order_id, assigned_batches.received as received, BatchProductDeals.batchName as batch_name, BatchProductDeals.boxes as boxes, BatchProductDeals.quantityPerBox as quantity_per_box, DistributorProducts.productName as product_name, Products.price as price from assigned_batches inner join BatchProductDeals on assigned_batches.batch_code=BatchProductDeals.batchQRCode inner join DistributorProducts on DistributorProducts.productId=BatchProductDeals.productId inner join Products on Products.id=DistributorProducts.productId where assigned_batches.receiver=? ', array($request->business_id) );
        return $result;
    }


    public function add_batch_to_shelf(Request $request){

        #get order details
        $batches = DB::select('select * from assigned_batches where batch_code=? ', array($request->batch_code) );
        foreach($batches as $batch){
            $order_id = $batch->id;
            $sender = $batch->sender;
            $receiver = $batch->receiver;

        }

        #get more details
        $result = DB::select('select * from Boxes where batchCode=? ', array($request->batch_code) );
        foreach($result as $r){
            $box_code = $r->boxQrCode;
            $qty = $r->productQty;
            $product_id = $r->productId;
            $total_price = $request->price * $qty;
            #insert boxes
            DB::insert('insert into RetailerBoxes(orderId, boxQrCode, productQty, retailer, distributor, buyingPrice, sellingPrice, totalPrice, productId, batchCode) values(?,?,?,?,?,?,?,?,?,?) ', array($order_id, $box_code, $qty, $receiver, $sender, $request->price, $request->selling_price, $total_price, $product_id, $request->batch_code ) );
            #insert products
            $products = DB::select('select * from BoxProducts where boxQrCode=? and batchCode=? ', array($box_code,$request->batch_code) );
            foreach($products as $pr){
                $product_code = $pr->productQrCode;
                DB::insert('insert into RetailerBoxesProducts(batchCode,boxQrCode,productId,productQrCode,buyingPrice,sellingPrice,retailer, distributor,buyer) values(?,?,?,?,?,?,?,?,?) ', array($request->batch_code, $box_code,$product_id, $product_code, $request->price, $request->selling_price, $receiver, $sender,0) );
            }
        }

        return 'success';

    }


    public function check_batch(Request $request){

        $result = DB::select('select * from RetailerBoxes where batchCode=? ', array($request->batch_code) );
        if(count($result)>0){
            return 1;
        }else{
            return 0;
        }

    }


    public function get_products_to_add(Request $request){
        $result = DB::select('select * from Products order by productName asc ');
        return $result;
    }

    public function add_product(Request $request){

        $check = DB::select('select * from DistributorProducts where productId=? and distributor=? ', array($request->product_id, $request->business_id) );

        if(count($check)>0){
            return 'Product already exists in your account';
        }else{

        #get product details
        $result = DB::select('select * from Products where id=? ', array($request->product_id) );
        foreach($result as $r){
            DB::insert('insert into DistributorProducts(productId,distributor,manufacturer,productName,sellingPrice) values(?,?,?,?,?) ', array($request->product_id, $request->business_id, $r->ownerId, $r->productName, $request->selling_price) );
            return 'Product added to account';
        }

        }


    }



    public function add_retailer_product(Request $request){

        $check = DB::select('select * from RetailerProducts where productId=? and retailer=? ', array($request->product_id, $request->business_id) );

        if(count($check)>0){
            return 'Product already exists in your account';
        }else{

        #get product details
        $result = DB::select('select * from Products where id=? ', array($request->product_id) );
        foreach($result as $r){
            DB::insert('insert into RetailerProducts(productId,retailer,distributor,productName,sellingPrice) values(?,?,?,?,?) ', array($request->product_id, $request->business_id, $r->ownerId, $r->productName, $request->selling_price) );
            return 'Product added to account';
        }

        }


    }


    public function d_products(Request $request){
        $result = DB::select('select * from DistributorProducts where distributor=? ', array($request->business_id) );
        return $result;
    }

    public function r_products(Request $request){
        $result = DB::select('select * from RetailerProducts where retailer=? ', array($request->business_id) );
        return $result;
    }

       public function d_supplier_products(Request $request){
        $result = DB::select('select * from DistributorProducts where distributor=? order by productName asc ', array($request->supplier_id));
        return $result;
    }



     public function create_retailer_order(Request $request){
        #get total qty
        $total_quantity = intval($request->boxes) * intval($request->quantity_per_box);
        #get total price for order
        $total_price = 0;
        $result = DB::select('select * from DistributorProducts where productId=? ', array($request->product_id));
        if(count($result)>0){
            foreach($result as $r){
                $total_price = intval($r->sellingPrice) * intval($total_quantity);
                $product_name = $r->productName;
            }
        }


        #check if account type is retailer and has products on retailer_products
        if($request->account_type=='Retailer'){
          $data = DB::select('select * from RetailerProducts where retailer=? and productId=? ', array($request->business_id,$request->product_id) );
          if(count($data)==0){
            $message = 'For you to make this order you need to register the selected product on your products section';
          }else{
          #everything is fine proceed

        DB::insert('insert RetailerOrders(supplier_id, owner_id, delivery_date, quantity, product_id, specifications, instructions, deliveryLocation, totalPrice, boxes, quantity_per_box,productName) values(?,?,?,?,?,?,?,?,?,?,?,?) ', array($request->supplier_id, $request->business_id, $request->delivery_date, $total_quantity, $request->product_id, $request->specifications, $request->instructions, $request->delivery_location, $total_price, $request->boxes, $request->quantity_per_box,$product_name) );

            $message = 'Order created';
          }
        }

        return $message;

    }


      public function retailer_orders(Request $request){
        $result = DB::select('select Accounts.name as distributor_name, Accounts.email as email, Accounts.location as distributor_location, RetailerOrders.productName as product_name, RetailerOrders.delivery_date as delivery_date, RetailerOrders.quantity as quantity, RetailerOrders.deliveryLocation as delivery_location, RetailerOrders.status as status, RetailerOrders.totalPrice as total_price, RetailerOrders.id as order_id from Accounts inner join RetailerOrders on Accounts.id=RetailerOrders.owner_id where RetailerOrders.supplier_id=? ', array($request->business_id) );

        return $result;
    }


    public function retailer_order_details(Request $request){
           $result = DB::select('select Accounts.name as distributor_name, Accounts.email as email, Accounts.location as distributor_location, RetailerOrders.productName as product_name, RetailerOrders.delivery_date as delivery_date, RetailerOrders.quantity as quantity, RetailerOrders.deliveryLocation as delivery_location, RetailerOrders.status as status, RetailerOrders.totalPrice as total_price, RetailerOrders.id as order_id, RetailerOrders.paid as paid, RetailerOrders.specifications as specifications, RetailerOrders.instructions as instructions, RetailerOrders.created as created, RetailerOrders.owner_id as owner_id, RetailerOrders.product_id as product_id, RetailerOrders.supplier_id as supplier_id from Accounts inner join RetailerOrders on Accounts.id=RetailerOrders.owner_id where RetailerOrders.id=? ', array($request->id) );

           return $result;
       }


       public function boxes_to_assign(Request $request){
         #to get boxes to assign we need to get batches of the distributor first
         $result = DB::select('select assigned_batches.batch_code as batch_code, Boxes.boxQrCode as box_code, Boxes.productQty as qty, Products.productName as product_name from assigned_batches inner join Boxes on assigned_batches.batch_code=Boxes.batchCode inner join Products on Products.id=Boxes.productId where assigned_batches.receiver=? and Boxes.productId=? and Boxes.used=? ', array($request->business_id,$request->product_id,'no') );
         return $result;
     }

     public function add_box_to_order(Request $request){
       #get batch code
       $batch = DB::select('select * from Boxes where boxQrCode=? ', array($request->box_code) );
       if (count($batch)>0) {
         foreach ($batch as $b) {
           $batch_code = $b->batchCode;
           $qty = $b->productQty;
         }
       }
       #get buying price
       $result = DB::select('select * from DistributorProducts where productId=? ', array($request->product_id) );
       if(count($result)>0){
         foreach($result as $r){
           $price = $r->sellingPrice;
           $total_price = $price * $qty;
         }
       }

       DB::insert('insert into RetailerBoxes(orderId, boxQrCode, productQty, retailer, distributor, buyingPrice, sellingPrice, totalPrice, productId, batchCode) values(?,?,?,?,?,?,?,?,?,?)', array($request->order_id, $request->box_code, $qty, $request->receiver, $request->sender, $price, 0, $total_price, $request->product_id, $batch_code) );
       DB::update('update Boxes set used=? where boxQrCode=? ', array('Yes',$request->box_code) );

       #get products in the box and insert to RetailerBoxesProducts
       $products  = DB::select('select * from BoxProducts where boxQrCode=? ', array($request->box_code) );
       if(count($products)>0){

         foreach($products as $product){
           DB::insert('insert into RetailerBoxesProducts(batchCode,boxQrCode,productId,productQrCode,buyingPrice,sellingPrice,retailer,distributor) values(?,?,?,?,?,?,?,?) ', array($batch_code,$request->box_code, $request->product_id, $product->productQrCode, $price, 0, $request->receiver, $request->sender ) );
         }

       }
       DB::update('update RetailerOrders set status=? where id=? ', array(1, $request->order_id) );
       return 'success';

     }

     public function assigned_boxes(Request $request){
      $result = DB::select('select RetailerBoxes.productQty as qty, RetailerBoxes.received as received, RetailerBoxes.boxQrCode as box_code, RetailerProducts.sellingPrice as selling_price, RetailerProducts.productName as product_name from RetailerBoxes inner join RetailerProducts on RetailerBoxes.productId=RetailerProducts.productId where orderId=? ', array($request->order_id) );
      return $result;
     }

     public function delete_assigned_box(Request $request){
       DB::delete('delete from RetailerBoxes where boxQrCode=? ', array($request->box_code) );
       DB::update('update Boxes set used=? where boxQrCode=? ', array('No',$request->box_code) );
       DB::delete('delete from RetailerBoxesProducts where boxQrCode=? ', array($request->box_code) );
       return 'success';
     }

     public function change_retailer_order_status(Request $request){
       DB::update('update RetailerOrders set status=? where id=? ', array($request->status,$request->order_id) );
     }



     public function generate_box_qr(Request $request){
       $result = DB::select('select * from Boxes where batchCode=? ', array($request->batch_code));
       return $result;
     }

     public function generate_product_qr(Request $request){
       $result = DB::select('select * from BoxProducts where boxQrCode=? ', array($request->box_code));
       return $result;
     }


     public function retailer_outgoing_orders(Request $request){
      $result = DB::select('select Accounts.name as distributor_name, Accounts.email as email, Accounts.location as distributor_location, RetailerOrders.productName as product_name, RetailerOrders.delivery_date as delivery_date, RetailerOrders.quantity as quantity, RetailerOrders.deliveryLocation as delivery_location, RetailerOrders.status as status, RetailerOrders.totalPrice as total_price, RetailerOrders.id as order_id, RetailerOrders.supplier_id as supplier_id from Accounts inner join RetailerOrders on Accounts.id=RetailerOrders.owner_id where RetailerOrders.owner_id=? ', array($request->business_id) );

      return $result;
  }


  public function cart_details(Request $request){
  $cart = $request->cart;
  $result = [];
  foreach($cart as $c){
    $data = DB::select('select  RetailerProducts.sellingPrice as selling_price,RetailerBoxesProducts.productQrCode as product_code, RetailerProducts.productName as product_name from RetailerBoxesProducts inner join RetailerProducts on RetailerProducts.productId=RetailerBoxesProducts.productId where RetailerBoxesProducts.productQrCode=? ', array($c) );
    $result = array_merge($result,$data);
  }
    return $result;
  }

  public function check_qr(Request $request){
    $result = DB::select('select * from RetailerBoxesProducts where productQrCode=? and bought=? ', array($request->qr,'No') );
    if(count($result)>0){
      return 'success';
    }else{
      return 'fail';
    }
  }




  public function pay_shopping(Request $request){
    #generate order id
      $order_id = rand(100,1000) + rand(1000,10000) +rand(10,100);
    #get product details
    $cart = $request->cart;
    $result = [];
    foreach($cart as $c){
      $data = DB::select('select  RetailerProducts.sellingPrice as selling_price, RetailerProducts.productName as product_name, RetailerBoxesProducts.productQrCode as product_code, RetailerBoxesProducts.retailer as retailer from RetailerBoxesProducts inner join RetailerProducts on RetailerProducts.productId=RetailerBoxesProducts.productId where RetailerBoxesProducts.productQrCode=? ', array($c) );
      $result = array_merge($result,$data);
    }

    #balance
    $balance = 0;
    $details = DB::select('select * from Users where id=?',  array($request->user_id) );
    if(count($details)>0){
      foreach($details as $d){
        $balance  = $d->balance;
      }
    }

    $total_price = 0;
    $retailer = 0;
    foreach($result as $product){
      #get retailer id
      $retailer = $product->retailer;
      $total_price += $product->selling_price;
    }

    if($total_price>$balance){
      return 'fail';
    }else{
      $new_balance = $balance - $total_price;
      DB::update('update Users set balance=? where id=? ', array($new_balance,$request->user_id) );

      #mark products bought and used
      foreach($result as $product){
        DB::update('update RetailerBoxesProducts set bought=?, used=?, buyer=?, date_bought=? where productQrCode=? ', array('Yes','Yes',$request->user_id,date('Y-m-d'),$product->product_code) );
        #link order id to products
        DB::update('update RetailerBoxesProducts set shopping_order_id=?  where productQrCode=? ', array($order_id,$product->product_code) );
      }

      #record transaction

      DB::insert('insert into orderPayments(orderId, amount, paidBy, paidTo, comment) values(?,?,?,?,?) ', array($order_id,$total_price,$request->user_id,$retailer,'Amount paid for shopping') );

      DB::insert('insert into shopping_orders(order_id,total_amount,date_bought,buyer,retailer) values(?,?,?,?,?) ', array($order_id, $total_price,date('Y-m-d'), $request->user_id,$retailer ) );

      return 'success';
    }

  }






  public function pay_website_order(Request $request){
    #generate order id
      $order_id = rand(100,1000) + rand(1000,10000) +rand(10,100);
    #get product details
    $cart = $request->cart;
    $result = [];
    foreach($cart as $c){
      $data = DB::select('select * from RetailerProducts where productId=?', array($c) );
      $result = array_merge($result,$data);
    }

    #balance
    $balance = 0;
    $details = DB::select('select * from Users where id=?',  array($request->user_id) );
    if(count($details)>0){
      foreach($details as $d){
        $balance  = $d->balance;
      }
    }

    $total_price = 0;
    $retailer = 0;
    foreach($result as $product){
      #get retailer id
      $retailer = $product->retailer;
      $total_price += $product->sellingPrice;
    }

    if($total_price>$balance){
      return 'fail';
    }else{
      $new_balance = $balance - $total_price;
      DB::update('update Users set balance=? where id=? ', array($new_balance,$request->user_id) );

      



      #record transaction

      DB::insert('insert into orderPayments(orderId, amount, paidBy, paidTo, comment) values(?,?,?,?,?) ', array($order_id,$total_price,$request->user_id,$retailer,'Amount paid for shopping') );

      DB::insert('insert into shopping_orders(order_id,total_amount,date_bought,buyer,retailer,website_order) values(?,?,?,?,?,?) ', array($order_id, $total_price,date('Y-m-d'), $request->user_id,$retailer,1) );

      #insert items into website_orders_items table
      foreach($result as $r){
        DB::insert('insert website_orders_items(product_id,product_name,price,order_id) values(?,?,?,?) ', array($r->productId,$r->productName,$r->sellingPrice,$order_id));
      }

      return 'success';
    }

  }









  public function manufacturer_inventory(Request $request){

    $result = DB::table('Products')
    ->join('stock','stock.product_id','=','Products.id')
    ->select(DB::raw('count(stock.used) as total_available,Products.productName as product_name, Products.price as price, Products.productionCost as production_cost, (Products.price-Products.productionCost) as profit, (count(stock.used)*Products.price) as inventory_value, Products.reorder_level as reorder_level'))
    ->where('stock.business_id','=',$request->business_id)
    ->where('stock.used','=','No')
    ->groupBy('Products.productName', 'Products.price', 'Products.productionCost','Products.reorder_level')
    ->get();
    return $result;
  }


    public function distributor_inventory(Request $request){

      $result = DB::table('assigned_batches')
      ->join('Boxes','Boxes.batchCode','=','assigned_batches.batch_code')
      ->join('DistributorProducts','Boxes.productId','=','DistributorProducts.productId')
      ->join('Products','DistributorProducts.productId','=','Products.id')
      ->select(DB::raw('count(Boxes.batchCode) as total_boxes, DistributorProducts.productName as product_name, Products.price as buying_price, DistributorProducts.sellingPrice as selling_price, (DistributorProducts.sellingPrice-Products.price) as profit, (sum(Boxes.productQty)*DistributorProducts.sellingPrice) as inventory_value, sum(Boxes.productQty) as total_products, DistributorProducts.reorder_level as reorder_level   '))
      ->where('assigned_batches.receiver','=',$request->business_id)
      ->where('Boxes.used','=','No')
      ->groupBy('DistributorProducts.productName','Products.price', 'DistributorProducts.sellingPrice', 'DistributorProducts.reorder_level' )
      ->get();
      return $result;
    }

    public function retailer_inventory(Request $request){

      $result = DB::table('RetailerBoxesProducts')
      ->join('RetailerProducts','RetailerBoxesProducts.productId','=','RetailerProducts.productId')
      ->select(DB::raw('count(RetailerBoxesProducts.productId) as total_products, RetailerProducts.productName as product_name, RetailerBoxesProducts.buyingPrice as buying_price, RetailerProducts.sellingPrice as selling_price,RetailerProducts.reorderLevel as reorder_level,(RetailerProducts.sellingPrice-RetailerBoxesProducts.buyingPrice) as profit, (count(RetailerBoxesProducts.productId)*RetailerProducts.sellingPrice) as inventory_value'))
      ->where('RetailerBoxesProducts.retailer','=',$request->business_id)
      ->where('RetailerBoxesProducts.bought','=','No')
      ->groupBy('RetailerBoxesProducts.productId','RetailerProducts.productName','RetailerBoxesProducts.buyingPrice','RetailerProducts.sellingPrice','RetailerProducts.reorderLevel')
      ->get();
      return $result;
    }

    public function product_details(Request $request){
      $result = DB::table('Products')
      ->where('id','=',$request->product_id)
      ->get();
      return $result;
    }

    public function edit_product(Request $request){

      DB::update('update Products set productName=?, category=?, productionCost=?, price=?, dateManufactured=?, expiryDate=?, weight=?, shippingCostPerItem=?, description=?, reorder_level=? where id=? ', array($request->product_name, $request->category, $request->production_cost, $request->price, $request->date_manufactured, $request->expiry_date, $request->weight, $request->shipping_cost, $request->description, $request->reorder_level, $request->product_id ) );

      if($request->image!=''){
        $image=time().rand(100,10000).'.'.$request->image->extension();
        $req=$request->image->move('assets/product-images/', $image);
        DB::update('update Products set productPicture=? where id=? ', array($image, $request->product_id) );
      }

      return 'success';

    }

    public function d_product_details(Request $request){
      $result = DB::table('DistributorProducts')->where('id','=',$request->id)->get();
      return $result;
    }

    public function r_product_details(Request $request){
      $result = DB::table('RetailerProducts')->where('id','=',$request->id)->get();
      return $result;
    }

    public function edit_d_product(Request $request){
      DB::update('update DistributorProducts set reorder_level=?, sellingPrice=? where id=? ', array($request->reorder_level,$request->selling_price,$request->product_id) );
      return 'success';
    }

    public function edit_r_product(Request $request){
      DB::update('update RetailerProducts set reorderLevel=?, sellingPrice=? where id=? ', array($request->reorder_level,$request->selling_price,$request->product_id) );
      return 'success';
    }


    public function get_order_source(Request $request){
      $result = DB::select('select Accounts.name as distributor_name, Accounts.email as email, Accounts.location as distributor_location, Orders.productName as product_name, Orders.delivery_date as delivery_date, Orders.quantity as quantity, Orders.deliveryLocation as delivery_location, Orders.status as status, Orders.totalPrice as total_price, Orders.id as order_id from Accounts inner join Orders on Accounts.id=Orders.owner_id where Orders.supplier_id=? and Accounts.accountType=? ', array($request->business_id,$request->order_source) );

      return $result;
    }


    public function fetch_manufacturers(Request $request){
      $result = DB::select('select * from Accounts where accountType=?  order by name asc ', array('Manufacturer') );
      return $result;
    }

    public function fetch_distributors(Request $request){
      $result = DB::select('select * from Accounts where accountType=?  order by name asc ', array('Distributor') );
      return $result;
    }


    public function fetch_retailers(Request $request){
      $result = DB::select('select * from Accounts where accountType=?  order by name asc ', array('Retailer') );
      return $result;
    }

    public function update_stock(Request $request){
      $hash = md5(time().$request->product_id);

      DB::insert('insert into ProductStock(account_id,product_id,amount,hash) values(?,?,?,?) ', array($request->business_id, $request->product_id, $request->qty,$hash));
      $counter = 0;
      while($counter<$request->qty){
      $product_code = md5($counter.rand(12000,430200).time().rand(3000,90000));
      DB::insert('insert into stock(business_id,product_id,hash,product_code) values(?,?,?,?)', array($request->business_id, $request->product_id, $hash, $product_code) );
      $counter++;
      }
      return 'success';
    }

    public function stock_history(Request $request){
      $result = DB::select('select * from ProductStock where product_id=? and account_id=? ', array($request->product_id, $request->business_id) );
      return $result;
    }

    public function delete_stock(Request $request){
      DB::delete('delete from ProductStock where hash=? ', array($request->hash) );
      DB::delete('delete from stock where hash=? ', array($request->hash) );
      return 'success';
    }


    public function fetch_manufacturer_products(Request $request){
      $result = DB::select('select * from Products where ownerId=? ', array($request->business_id) );
      return $result;
    }

    public function fetch_distributor_products(Request $request){
      $result = DB::select('select DistributorProducts.productName as product_name, DistributorProducts.sellingPrice as selling_price, Products.productPicture as image, Products.description as description from DistributorProducts inner join Products on DistributorProducts.productId=Products.id where DistributorProducts.distributor=? ', array($request->business_id) );
      return $result;
    }


    public function fetch_retailer_products(Request $request){
      $result = DB::select('select  RetailerProducts.productName as product_name, RetailerProducts.sellingPrice as selling_price,Products.id as id, Products.productPicture as image, Products.description as description  from RetailerProducts inner join Products on Products.id=RetailerProducts.productId where RetailerProducts.retailer=? ', array($request->business_id) );
      return $result;
    }

    public function delete_product(Request $request){
      $result = DB::select('select * from stock where product_id=? ', array($request->id) );
      $result_1 = DB::select('select * from BatchProductDeals where productId=? ', array($request->id) );
      if(count($result)>0){
        return 'Product cannot be deleted!Its already in stock.Delete it from stock';
      }else if(count($result_1)>0){
        return 'Product cannot be deleted!Its already in batch.Delete it from batches';
      }else{
        DB::delete('delete from Products where id=? ', array($request->id) );
        return 'Product removed from list';
      }

    }


    public function view_website_cart(Request $request){
        $result = [];
        foreach($request->cart as $c){
            $data = DB::select('select * from RetailerProducts where productId=? ', array($c) );
            $result = array_merge($result,$data);
        }
        return $result;
    }

    public function add_item_to_order(Request $request){
        $result = DB::select('select * from RetailerBoxesProducts where productQrCode=? and used=? and bought=? and retailer=? ', array($request->qr,'No','No',$request->retailer) );
        if(count($result)==0){
            return 'fail';
        }else{
             #mark products bought and used
      foreach($result as $product){
        DB::update('update RetailerBoxesProducts set bought=?, used=?, buyer=?, date_bought=? where productQrCode=? ', array('Yes','Yes',$request->buyer,date('Y-m-d'),$request->qr) );
        #link order id to products
        DB::update('update RetailerBoxesProducts set shopping_order_id=?  where productQrCode=?', array($request->order_id,$request->qr) );
      }
      return 'success';
        }
    }


    public function delete_website_order(Request $request){
        DB::delete('delete from shopping_orders where order_id=?', array($request->order_id));
        return 'success!';
    }


    public function my_shopping_orders(Request $request){
        $result = DB::select('select * from shopping_orders where buyer=? ', array($request->buyer) );
        return $result;
    }

    /**
     * Aggregated product feed for external apps (clients & businesses).
     * audience=client  → retailer catalog (shoppable)
     * audience=business → manufacturer catalog (wholesale / supply)
     */
    public function products_feed(Request $request){
      $audience = strtolower(trim($request->query('audience', 'client')));
      $limit = intval($request->query('limit', 24));
      if($limit < 1){ $limit = 24; }
      if($limit > 100){ $limit = 100; }
      $offset = intval($request->query('offset', 0));
      if($offset < 0){ $offset = 0; }
      $search = trim($request->query('search', ''));

      if($audience === 'business'){
        $query = DB::table('Products')
          ->join('Accounts', 'Accounts.id', '=', 'Products.ownerId')
          ->select(
            'Products.id as product_id',
            'Products.productName as product_name',
            'Products.price as selling_price',
            'Products.productPicture as image',
            'Products.description as description',
            'Products.category as category',
            'Accounts.id as business_id',
            'Accounts.name as business_name',
            'Accounts.location as business_location',
            'Accounts.accountType as business_type'
          )
          ->where('Accounts.accountType', 'Manufacturer')
          ->orderBy('Products.productName', 'asc');

        if($search !== ''){
          $query->where(function($q) use ($search){
            $q->where('Products.productName', 'like', '%'.$search.'%')
              ->orWhere('Accounts.name', 'like', '%'.$search.'%')
              ->orWhere('Products.category', 'like', '%'.$search.'%');
          });
        }

        $total = (clone $query)->count();
        $products = $query->offset($offset)->limit($limit)->get();
        $nextOffset = $offset + count($products);
        return response()->json([
          'audience' => 'business',
          'count' => count($products),
          'total' => $total,
          'limit' => $limit,
          'offset' => $offset,
          'has_more' => $nextOffset < $total,
          'next_offset' => $nextOffset < $total ? $nextOffset : null,
          'products' => $products,
        ]);
      }

      $query = DB::table('RetailerProducts')
        ->join('Products', 'Products.id', '=', 'RetailerProducts.productId')
        ->join('Accounts', 'Accounts.id', '=', 'RetailerProducts.retailer')
        ->select(
          'Products.id as product_id',
          'RetailerProducts.productName as product_name',
          'RetailerProducts.sellingPrice as selling_price',
          'Products.productPicture as image',
          'Products.description as description',
          'Products.category as category',
          'Accounts.id as business_id',
          'Accounts.name as business_name',
          'Accounts.location as business_location',
          'Accounts.accountType as business_type'
        )
        ->where('Accounts.accountType', 'Retailer')
        ->orderBy('RetailerProducts.productName', 'asc');

      if($search !== ''){
        $query->where(function($q) use ($search){
          $q->where('RetailerProducts.productName', 'like', '%'.$search.'%')
            ->orWhere('Accounts.name', 'like', '%'.$search.'%')
            ->orWhere('Products.category', 'like', '%'.$search.'%');
        });
      }

      $total = (clone $query)->count();
      $products = $query->offset($offset)->limit($limit)->get();
      $nextOffset = $offset + count($products);
      return response()->json([
        'audience' => 'client',
        'count' => count($products),
        'total' => $total,
        'limit' => $limit,
        'offset' => $offset,
        'has_more' => $nextOffset < $total,
        'next_offset' => $nextOffset < $total ? $nextOffset : null,
        'products' => $products,
      ]);
    }

    /**
     * Single product detail for external shop apps.
     * audience=client → retailer catalog; audience=business → manufacturer catalog
     */
    public function feed_product_detail(Request $request){
      $product_id = intval($request->query('product_id', 0));
      $business_id = intval($request->query('business_id', 0));
      $audience = strtolower(trim($request->query('audience', 'client')));

      if($product_id < 1 || $business_id < 1){
        return response()->json(['error' => 'product_id and business_id are required'], 400);
      }

      if($audience === 'business'){
        $product = DB::table('Products')
          ->join('Accounts', 'Accounts.id', '=', 'Products.ownerId')
          ->select(
            'Products.id as product_id',
            'Products.productName as product_name',
            'Products.price as selling_price',
            'Products.productPicture as image',
            'Products.description as description',
            'Products.category as category',
            'Products.weight as weight',
            'Products.shippingCostPerItem as shipping_cost',
            'Accounts.id as business_id',
            'Accounts.name as business_name',
            'Accounts.location as business_location',
            'Accounts.email as business_email',
            'Accounts.accountType as business_type'
          )
          ->where('Products.id', $product_id)
          ->where('Products.ownerId', $business_id)
          ->where('Accounts.accountType', 'Manufacturer')
          ->first();
      }else{
        $product = DB::table('RetailerProducts')
          ->join('Products', 'Products.id', '=', 'RetailerProducts.productId')
          ->join('Accounts', 'Accounts.id', '=', 'RetailerProducts.retailer')
          ->select(
            'Products.id as product_id',
            'RetailerProducts.productName as product_name',
            'RetailerProducts.sellingPrice as selling_price',
            'Products.productPicture as image',
            'Products.description as description',
            'Products.category as category',
            'Products.weight as weight',
            'Products.shippingCostPerItem as shipping_cost',
            'Accounts.id as business_id',
            'Accounts.name as business_name',
            'Accounts.location as business_location',
            'Accounts.email as business_email',
            'Accounts.accountType as business_type'
          )
          ->where('RetailerProducts.productId', $product_id)
          ->where('RetailerProducts.retailer', $business_id)
          ->first();
      }

      if(!$product){
        return response()->json(['error' => 'Product not found'], 404);
      }

      return response()->json(['product' => $product, 'audience' => $audience === 'business' ? 'business' : 'client']);
    }

    /**
     * Checkout from external apps (e.g. BeautyExpress) without VP wallet balance.
     * Body: buyer_email, buyer_name, buyer_phone, external_user_id, payment_method,
     *       items: [{ product_id, retailer_id, quantity }]
     */
    public function external_website_checkout(Request $request){
      $buyer_email = strtolower(trim($request->buyer_email ?? ''));
      $buyer_name = trim($request->buyer_name ?? '');
      $buyer_phone = trim($request->buyer_phone ?? '');
      $payment_method = trim($request->payment_method ?? 'external');
      $items = $request->items;

      if($buyer_email === '' || $buyer_name === ''){
        return response()->json(['error' => 'buyer_email and buyer_name are required'], 400);
      }
      if(!is_array($items) || count($items) === 0){
        return response()->json(['error' => 'Cart items are required'], 400);
      }

      # find or create VP user for this buyer
      $users = DB::select('select * from Users where LOWER(email)=? limit 1', array($buyer_email));
      if(count($users) > 0){
        $buyer_id = $users[0]->id;
        if($buyer_phone !== ''){
          DB::update('update Users set phoneNumber=? where id=?', array($buyer_phone, $buyer_id));
        }
      }else{
        $parts = preg_split('/\s+/', $buyer_name, 2);
        $first = $parts[0] ?? $buyer_name;
        $last = $parts[1] ?? '';
        DB::insert(
          'insert into Users(userType,fullName,firstName,lastName,email,phoneNumber,password,address) values(?,?,?,?,?,?,?,?)',
          array(0, $buyer_name, $first, $last, $buyer_email, $buyer_phone, bcrypt(uniqid('be_', true)), 'BeautyExpress')
        );
        $created = DB::select('select id from Users where LOWER(email)=? limit 1', array($buyer_email));
        $buyer_id = $created[0]->id;
      }

      # resolve line items grouped by retailer
      $lines_by_retailer = [];
      foreach($items as $item){
        $product_id = intval($item['product_id'] ?? 0);
        $retailer_id = intval($item['retailer_id'] ?? 0);
        $qty = intval($item['quantity'] ?? 1);
        if($product_id < 1 || $retailer_id < 1 || $qty < 1){
          return response()->json(['error' => 'Invalid cart item'], 400);
        }

        $row = DB::select(
          'select RetailerProducts.productId as product_id, RetailerProducts.productName as product_name, RetailerProducts.sellingPrice as selling_price, RetailerProducts.retailer as retailer from RetailerProducts where productId=? and retailer=? limit 1',
          array($product_id, $retailer_id)
        );
        if(count($row) === 0){
          return response()->json(['error' => 'Product '.$product_id.' not found for retailer '.$retailer_id], 404);
        }

        if(!isset($lines_by_retailer[$retailer_id])){
          $lines_by_retailer[$retailer_id] = [];
        }
        for($i = 0; $i < $qty; $i++){
          $lines_by_retailer[$retailer_id][] = $row[0];
        }
      }

      $orders = [];
      foreach($lines_by_retailer as $retailer_id => $lines){
        $order_id = rand(100,1000) + rand(1000,10000) + rand(10,100);
        $total_price = 0;
        foreach($lines as $line){
          $total_price += floatval($line->selling_price);
        }

        DB::insert(
          'insert into shopping_orders(order_id,total_amount,date_bought,buyer,retailer,website_order) values(?,?,?,?,?,?)',
          array($order_id, $total_price, date('Y-m-d'), $buyer_id, $retailer_id, 1)
        );

        foreach($lines as $line){
          DB::insert(
            'insert into website_orders_items(product_id,product_name,price,order_id) values(?,?,?,?)',
            array($line->product_id, $line->product_name, $line->selling_price, $order_id)
          );
        }

        DB::insert(
          'insert into orderPayments(orderId, amount, paidBy, paidTo, comment) values(?,?,?,?,?)',
          array($order_id, $total_price, $buyer_id, $retailer_id, 'BeautyExpress checkout ('.$payment_method.')')
        );

        $orders[] = [
          'order_id' => $order_id,
          'retailer_id' => $retailer_id,
          'total_amount' => $total_price,
          'item_count' => count($lines),
        ];
      }

      return response()->json([
        'success' => true,
        'buyer_id' => $buyer_id,
        'orders' => $orders,
      ]);
    }

    /**
     * Business/salon checkout from manufacturer catalog (BeautyExpress admin).
     * Creates/finds a Retailer Account for the salon and places Orders to manufacturers.
     */
    public function external_business_checkout(Request $request){
      $buyer_email = strtolower(trim($request->buyer_email ?? ''));
      $buyer_name = trim($request->buyer_name ?? '');
      $buyer_phone = trim($request->buyer_phone ?? '');
      $company_name = trim($request->company_name ?? $buyer_name);
      $location = trim($request->location ?? 'BeautyExpress');
      $payment_method = trim($request->payment_method ?? 'external');
      $delivery_location = trim($request->delivery_location ?? $location);
      $items = $request->items;

      if($buyer_email === '' || $buyer_name === ''){
        return response()->json(['error' => 'buyer_email and buyer_name are required'], 400);
      }
      if(!is_array($items) || count($items) === 0){
        return response()->json(['error' => 'Cart items are required'], 400);
      }

      # find or create VP user
      $users = DB::select('select * from Users where LOWER(email)=? limit 1', array($buyer_email));
      if(count($users) > 0){
        $buyer_id = $users[0]->id;
        if($buyer_phone !== ''){
          DB::update('update Users set phoneNumber=? where id=?', array($buyer_phone, $buyer_id));
        }
        if($company_name !== ''){
          DB::update('update Users set companyName=?, fullName=? where id=?', array($company_name, $buyer_name, $buyer_id));
        }
      }else{
        $parts = preg_split('/\s+/', $buyer_name, 2);
        $first = $parts[0] ?? $buyer_name;
        $last = $parts[1] ?? '';
        DB::insert(
          'insert into Users(userType,fullName,firstName,lastName,email,phoneNumber,password,address,companyName) values(?,?,?,?,?,?,?,?,?)',
          array(1, $buyer_name, $first, $last, $buyer_email, $buyer_phone, bcrypt(uniqid('be_', true)), $location, $company_name)
        );
        $created = DB::select('select id from Users where LOWER(email)=? limit 1', array($buyer_email));
        $buyer_id = $created[0]->id;
      }

      # find or create Retailer account for this salon
      $accounts = DB::select(
        'select * from Accounts where userId=? and accountType=? limit 1',
        array($buyer_id, 'Retailer')
      );
      if(count($accounts) > 0){
        $account_id = $accounts[0]->id;
        DB::update(
          'update Accounts set name=?, email=?, location=? where id=?',
          array($company_name, $buyer_email, $location, $account_id)
        );
      }else{
        DB::insert(
          'insert into Accounts(name,email,location,accountType,userId) values(?,?,?,?,?)',
          array($company_name, $buyer_email, $location, 'Retailer', $buyer_id)
        );
        $new_account = DB::select(
          'select id from Accounts where userId=? and accountType=? order by id desc limit 1',
          array($buyer_id, 'Retailer')
        );
        $account_id = $new_account[0]->id;
      }

      $orders = [];
      foreach($items as $item){
        $product_id = intval($item['product_id'] ?? 0);
        $manufacturer_id = intval($item['manufacturer_id'] ?? ($item['retailer_id'] ?? 0));
        $qty = intval($item['quantity'] ?? 1);
        if($product_id < 1 || $manufacturer_id < 1 || $qty < 1){
          return response()->json(['error' => 'Invalid cart item'], 400);
        }

        $product_rows = DB::select(
          'select Products.id as product_id, Products.productName as product_name, Products.price as price, Products.ownerId as manufacturer_id from Products where id=? and ownerId=? limit 1',
          array($product_id, $manufacturer_id)
        );
        if(count($product_rows) === 0){
          return response()->json(['error' => 'Product '.$product_id.' not found for manufacturer '.$manufacturer_id], 404);
        }
        $product = $product_rows[0];
        $total_price = floatval($product->price) * $qty;

        # ensure product is registered on retailer catalog (required by VP order rules)
        $existing = DB::select(
          'select * from RetailerProducts where productId=? and retailer=? limit 1',
          array($product_id, $account_id)
        );
        if(count($existing) === 0){
          DB::insert(
            'insert into RetailerProducts(productId,retailer,distributor,productName,sellingPrice) values(?,?,?,?,?)',
            array($product_id, $account_id, $manufacturer_id, $product->product_name, $product->price)
          );
        }

        DB::insert(
          'insert into Orders(supplier_id, owner_id, delivery_date, quantity, product_id, specifications, instructions, deliveryLocation, totalPrice, boxes, quantity_per_box, productName, paid) values(?,?,?,?,?,?,?,?,?,?,?,?,?)',
          array(
            $manufacturer_id,
            $account_id,
            date('Y-m-d', strtotime('+7 days')),
            $qty,
            $product_id,
            'BeautyExpress business order',
            'Ordered via BeautyExpress',
            $delivery_location,
            $total_price,
            $qty,
            1,
            $product->product_name,
            'Paid'
          )
        );

        $order_row = DB::select(
          'select id from Orders where owner_id=? and supplier_id=? and product_id=? order by id desc limit 1',
          array($account_id, $manufacturer_id, $product_id)
        );
        $order_id = $order_row[0]->id;

        DB::insert(
          'insert into orderPayments(orderId, amount, paidBy, paidTo, comment) values(?,?,?,?,?)',
          array($order_id, $total_price, $buyer_id, $manufacturer_id, 'BeautyExpress business checkout ('.$payment_method.')')
        );

        $orders[] = [
          'order_id' => $order_id,
          'manufacturer_id' => $manufacturer_id,
          'product_id' => $product_id,
          'total_amount' => $total_price,
          'quantity' => $qty,
        ];
      }

      return response()->json([
        'success' => true,
        'buyer_id' => $buyer_id,
        'account_id' => $account_id,
        'orders' => $orders,
      ]);
    }

}
