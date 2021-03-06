<?php

namespace App\Http\Controllers;

/******************************************************
* IM - Vocabulary Builder
* Version : 1.0.2
* Copyright© 2016 Imprevo Ltd. All Rights Reversed.
* This file may not be redistributed.
* Author URL:http://imprevo.net
******************************************************/

use App\User;
use App\Sale;
use App\Http\Requests;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Redirect;
use Illuminate\Support\Facades\DB;
use Log;
use App\Reward;
use App\Coin;

class RewardController extends Controller
{
  public function __construct()
  {
    $this->middleware(['auth', '2fa'] );
  }

  public function rewards(Request $request)
  {
    $rewards = Reward::paginate(50);
    foreach ($rewards as $reward){
      $referred_by = User::where('id', $reward->referral_id)->first();
      $reward->referred_by = $referred_by;
    }
    return view('rewards', [
      'rewards' => $rewards
    ]);
  }

  public function newReward()
  {
    return view('rewardEdit', [
      'reward' => array('id'=>null, 'user_id'=>'', 'reward_amount'=>'',  'masternode_id'=>''),
    ]);
  }

  public function editReward(Request $request, $id)
  {
    return view('rewardEdit', [
      'reward' => Reward::findOrNew($id),
    ]);
  }

  public function postEdit(Request $request)
  {
    $reward=[];
    if($request->input('id') != '') {
      $reward = Reward::findOrNew($request->input('id'));
      $reward->user_id = $request->input('user_id');
      $reward->reward_amount = $request->input('reward_amount');
      $reward->masternode_id = $request->input('masternode_id');
      $reward->save();
    } else {
      $reward = Reward::create([
        'user_id' => $request->input('user_id'),
        'reward_amount' => $request->input('reward_amount'),
        'masternode_id' => $request->input('masternode_id'),
      ]);
    }
    return redirect()->to('rewards');
  }

  public function destroy($id)
  {
    $u = Reward::findOrNew($id);
    //$this->authorize('destroy', $category);
    //Cat::destroy([$category]);
    $u->delete();
    $ret = array("result"=>"ok");
    return json_encode($ret);
  }
}
