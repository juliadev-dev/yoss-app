<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Product;
use App\Models\Customer;
use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Models\DetailTransaction;
use Illuminate\Support\Facades\DB;

class TransactionController extends Controller
{
    public function index()
    {
        $transactions = DB::table('transactions')
            ->select('*', 'transactions.id as transaction_id', 'customers.name as customer')
            ->leftjoin('customers', 'customers.id', '=', 'transactions.customer_id')
            ->orderBy('transactions.id', 'desc')
        ->paginate(5);

        // dd($transactions);
        return view('transaction.index', compact('transactions'));
    }

    public function create()
    {
        $now = Carbon::now()->format('d F Y');
        $transDate = Carbon::now()->format('Y-m-d');
        $products = Product::all();
        $customers = Customer::all();
        // dd($now);
        return view('transaction.create', compact('now', 'transDate', 'products', 'customers'));
    }

    public function store(Request $request)
    {
        // dd('hai');
        try {
            $validateData = $request->validate([
                'date' => 'required',
                'customer_id' => 'required',
                'store_id' => 'required',
                'user_id' => 'required',
                'status' => 'required ',
            ]);
            DB::beginTransaction();


            $insertData = Transaction::create($validateData);
            $id =  $insertData->id;
            $total_data = $request->get('rows1');
            
            for ($i = 1; $i <= count($total_data); $i++) {
                //insert data detail
                $insertDataDetails = new DetailTransaction;
                $insertDataDetails->transaction_id = $id;
                $insertDataDetails->product_id = $_POST['product_' . $i];
                $insertDataDetails->qty = $_POST['qty_' . $i];
                $insertDataDetails->save();

                $updateStock = Product::Where('id', $_POST['product_' . $i])
                ->decrement('stock', $_POST['qty_' . $i]);
            }
            DB::commit();

            // dd('berhasil');
            return redirect('priviewInvoice')->with('success', 'Transaksi Berhasil!');
        } catch (\Exception $e) {
            DB::rollback();
        }
    }

    public function priviewInvoice()
    {
        // dd('berhasil');
        $transactions = DB::table('transactions')
            ->select(
                '*',
                'transactions.id as transaction_id',
                'customers.name as customer',
                'customers.address as customers_address',
                'customers.phone as customers_phone',
                'stores.name as store_name',
                'stores.address as stores_address',
                'stores.phone as stores_phone',
            )
            ->leftjoin('customers', 'customers.id', '=', 'transactions.customer_id')
            ->leftjoin('stores', 'stores.id', '=', 'transactions.store_id')
            ->leftjoin('detail_transactions', 'detail_transactions.transaction_id', '=', 'transactions.id')
            ->leftjoin('products', 'products.id', '=', 'detail_transactions.product_id')
            ->orderBy('transactions.id', 'desc')
            ->first();

        $detail_transactions = DB::table('detail_transactions')
        ->leftjoin('products', 'products.id', '=', 'detail_transactions.product_id')
        ->where('detail_transactions.transaction_id', $transactions->transaction_id)
            ->get();

        $totalPrice = DB::table('detail_transactions')
        ->leftjoin('products', 'products.id', '=', 'detail_transactions.product_id')
        ->where('detail_transactions.transaction_id', $transactions->transaction_id)
            ->sum('products.price');

        // dd($totalPrice);
        return view('transaction.preview', compact('transactions', 'detail_transactions', 'totalPrice'));
    }

    public function detail($id)
    {
        dd($id);
        $transactions = DB::table('transactions')
        ->select(
            '*',
            'transactions.id as transaction_id',
            'customers.name as customer',
            'customers.address as customers_address',
            'customers.phone as customers_phone',
            'stores.name as store_name',
            'stores.address as stores_address',
            'stores.phone as stores_phone',
        )
            ->leftjoin('customers', 'customers.id', '=', 'transactions.customer_id')
            ->leftjoin('stores', 'stores.id', '=', 'transactions.store_id')
            ->leftjoin('detail_transactions', 'detail_transactions.transaction_id', '=', 'transactions.id')
            ->leftjoin('products', 'products.id', '=', 'detail_transactions.product_id')
            ->orderBy('transactions.id', 'desc')
            ->first();

        $detail_transactions = DB::table('detail_transactions')
        ->leftjoin('products', 'products.id', '=', 'detail_transactions.product_id')
        ->where('detail_transactions.transaction_id', $transactions->transaction_id)
            ->get();

        $totalPrice = DB::table('detail_transactions')
        ->leftjoin('products', 'products.id', '=', 'detail_transactions.product_id')
        ->where('detail_transactions.transaction_id', $transactions->transaction_id)
            ->sum('products.price');

        // dd($totalPrice);
        return view('transaction.detail', compact('transactions', 'detail_transactions', 'totalPrice'));
    }

    public function test()
    {
        return 'test';
    }
}
