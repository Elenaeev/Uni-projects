<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use App\Models\User;
use App\Models\Event;
use App\Models\EventParticipants;
use App\Models\EventProducts;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class EventsController extends Controller
{
    public function index()
    {
        $events = Event::whereNotNull('event_id')->get();

        return view('events', [
            "events" => $events
        ]);
    }

    public function show($event_id)
    {
        $event = Event::where('event_id', $event_id)->first();
        if(!$event)
            abort(404);
        
        $seller = User::where('user_id', $event->seller_id)->first();
        if(!$seller)
            abort(500);
        
        $user = User::where('user_id', Auth::user()->user_id)->first();
        if(!$user)
            abort(500);

        $eprods = $event->products()->get();
        

        return view('eventPage', [
            "event" => $event,
            "event_exinfo" => [
                'seller' => $seller, 
                'products' => $eprods]
        ]);
    }

    //get
    public function createPage()
    {
        if(!Gate::allows('be-seller'))
            abort(404);

        $user = User::where('user_id', Auth::user()->user_id)->first();
        if(!$user)
            abort(404);

        $soldProducts = $user->saleProducts()->get();

        return view("eventManager", [
            "create" => true,
            "event" => null,
            "soldProducts" => $soldProducts
        ]);

    }

    //post
    public function createEventData(Request $request)
    {
        if(!Gate::allows('be-seller'))
            abort(404);


        $request->validate([
            'ename' => ['required', 'max:64', 'min:2'],
            'edescr' => ['max:300'],
            'evStart' => ['required'],
            'evEnd' => ['required'],
            'evPlace' =>['required', 'max:64', 'min:3'],
            'image' => 'image|mimes:jpeg,jpg,png|dimensions:max_width=1440,min_width=100,max_height=2560,min_height=100',
            ]);

        $event = new Event();
        DB::transaction(function () use ($request, $event) 
        {
            $event->seller_id = Auth::user()->user_id;
            $event->name = $request->input('ename');
            $event->start_date = Carbon::parse($request->input('evStart'));
            $event->end_date = Carbon::parse($request->input('evEnd'));
            $event->address = $request->input('evPlace');
            $event->description = $request->input('edescr');
            $event->save();
    
            
            $eprods = $request->input("evProds", []); 
            foreach($eprods as $ep)
            {
                $eventProduct = new EventProducts();
                $eventProduct->event_id = $event->event_id;
                $eventProduct->product_id = intval($ep);
                $eventProduct->save();
            }

        });


        if(request()->image && $event != null)
            request()->image->move(public_path('web/images/events/'), $event->product_id.'.jpg');

        return  redirect()->route("events.createPage")->with(["message" => "Event succesfully created"]);
    }

    //get
    public function edit($event_id)
    {
        if(!Gate::allows('be-seller'))
            abort(404);

        $event = Event::where("event_id", $event_id)->first();
        if(!$event)
            abort(404);
        
        $user = User::where("user_id", Auth::user()->user_id)->first();
        if(!$user)
            abort(404);

        $soldProducts = $user->saleProducts()->get();

        return view("eventManager", [
            "create" => false,
            "event" => $event,
            "soldProducts" => $soldProducts
        ]);
    }

    //post
    public function saveEditedEventData(Request $request, $event_id)
    {
        if(!Gate::allows('be-seller'))
            abort(404);

        $request->validate([
            'ename' => ['required', 'max:64', 'min:2'],
            'edescr' => ['max:300'],
            'evStart' => ['required'],
            'evEnd' => ['required'],
            'evPlace' =>['required', 'max:64', 'min:3'],
            'image' => 'image|mimes:jpeg,jpg,png|dimensions:max_width=1440,min_width=100,max_height=2560,min_height=100',
            ]);

        $event = Event::where("event_id", $event_id)->first();
        if(!$event)
            abort(404);
        
        if($event->seller_id != Auth::user()->user_id)
            abort(404);

        $user = User::where("user_id", $event->seller_id)->first();
        if(!$user)
            abort(404);

        $soldProducts = $user->saleProducts()->get();


        DB::transaction(function () use ($request, $event, $soldProducts) 
        {
            $event->name = $request->input('ename');
            $event->start_date = Carbon::parse($request->input('evStart'));
            $event->end_date = Carbon::parse($request->input('evEnd'));
            $event->address = $request->input('evPlace');
            if($request->input('edescr') != null)
                $event->description =  $request->input('edescr');
            else
                $event->description = "";
            $event->save();
    
            
            foreach($soldProducts as $soldp)
            {
                $p = EventProducts::where([['event_id', '=', $event->event_id], ['product_id', '=', $soldp->product_id]])->first();
                if($p)
                    $p->delete();
            }

            $eprods = $request->input("evProds", []); 
            foreach($eprods as $ep)
            {
                $eventProduct = new EventProducts();
                $eventProduct->event_id = $event->event_id;
                $eventProduct->product_id = intval($ep);
                $eventProduct->save();
            }

        });


        if(request()->image && $event != null)
            request()->image->move(public_path('web/images/events/'), $event->event_id.'.jpg');

        return  redirect()->route("events.createPage")->with(["message" => "Event succesfully edited"]);
    }

    public function  add($event_id)
    {
        $user = User::where("user_id", Auth::user()->user_id)->first();
        if(!$user)
            abort(500);
        if($user->events()->where("events.event_id", $event_id)->exists())
            redirect()->route("events.show", ["event_id"=> $event_id])->withErrors(["event_add" => "Event is already added"]);

        $ep = new EventParticipants();
        $ep->user_id = Auth::user()->user_id;
        $ep->event_id = $event_id;
        $ep->save();

        return  redirect()->route("events.show", ["event_id"=> $event_id])->with(["message" => "Event addded"]);
    }

    public function  remove($event_id)
    {
        $user = User::where("user_id", Auth::user()->user_id)->first();
        if(!$user)
            abort(500);

        $epart = EventParticipants::where("user_id", Auth::user()->user_id)->where("event_id", $event_id)->first();
        if(!$epart)
            redirect()->route("events.show", ["event_id"=> $event_id])->withErrors(["event_remove" => "Event is not added"]);

        $epart->delete();

        return  redirect()->route("events.show", ["event_id"=> $event_id])->with(["message" => "Event removed"]);
    }
}
