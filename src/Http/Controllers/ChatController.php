<?php

namespace Deaduu\Livechat\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Deaduu\Livechat\Models\Chat;
use Deaduu\Livechat\Models\User;
use Auth;


class ChatController extends Controller
{
    public function index()
    {
        // dd(Auth::user());
        return view('livechat::chat');
    }

    public function chat()
    {
        $id = Auth::user()->id;

        $data = Chat::Where(
            function ($data) use ($id) {
                $data->orwhere('sender_id', $id);
                $data->orwhere('receiver_id', $id);
            }
        )
            ->join('users as s', 's.id', '=', 'chats.sender_id')
            ->join('users as r', 'r.id', '=', 'chats.receiver_id')
            ->groupBy('chats.thread')
            ->orderBy('chats.read_status', 'ASC')
            ->select('chats.*', 's.name as sender_name', 'r.name as receiver_name')
            ->get()
            ->map(function ($q) use ($id) {
                if ($q->sender_id == $id) {
                    $q->name = $q->receiver_name;
                    $q->sid = $q->receiver_id;
                    // $q->photo = $q->receiver_photo;
                } elseif ($q->receiver_id == $id) {
                    $q->name = $q->sender_name;
                    $q->sid = $q->sender_id;
                    // $q->photo = $q->sender_photo;
                }

                $q->time = date('h:i a', strtotime($q->created_at));
                $q->date = date('d M,y', strtotime($q->created_at));

                $q->message = substr(Chat::where('thread', $q->thread)->orderBy('created_at', 'DESC')->value('message'), 0, 6) . '...';

                $q->unread = Chat::Where(['thread' => $q->thread, 'receiver_id' => $id])->where('read_status', 0)->count();
                return $q;
            });

        return json_encode($data);
    }

    public function thread($thread)
    {
        $id = Auth::user()->id;

        $checkthread = Chat::Where('thread', $thread)->where(
            function ($data) use ($id) {
                $data->orwhere('sender_id', $id);
                $data->orwhere('receiver_id', $id);
            }
        )->count();

        // if ($checkthread > 20) {
        //     $skip = $checkthread - 20;
        // } else {
        //     $skip = 0;
        // }

        $skip = ($checkthread > 20) ? $checkthread - 20 : 0;

        if ($checkthread > 0) {

            $data = Chat::Where('thread', $thread)
                ->join('users as s', 's.id', '=', 'chats.sender_id')
                ->join('users as r', 'r.id', '=', 'chats.receiver_id')
                ->select('chats.*', 's.name as sender_name', 'r.name as receiver_name')
                ->orderBy('created_at', 'ASC')
                ->skip($skip)
                ->take(20)
                ->get()
                ->map(function ($q) use ($id) {
                    if ($q->sender_id == $id) {
                        $q->rec = 0;
                    } elseif ($q->receiver_id == $id) {
                        $q->sender_name = $q->sender_name;
                        $q->receiver_name = $q->receiver_name;
                        $q->rec = 1;
                    }
                    $q->time = date('h:i a d M,y', strtotime($q->created_at));
                    return $q;
                });
            Chat::Where(['thread' => $thread, 'receiver_id' => $id])->update(['read_status' => 1]);

            return json_encode($data);
        } else {
            return json_encode(['response' => 'no data']);
        }
    }

    public function fetchdata($thread)
    {
        $id = Auth::user()->id;

        $checkthread = Chat::Where('thread', $thread)->where(
            function ($data) use ($id) {
                $data->orwhere('sender_id', $id);
                $data->orwhere('receiver_id', $id);
            }
        )->exists();

        $data['unread'] = Chat::where(['receiver_id' => $id, 'read_status' => 0])
            ->groupBy('thread')
            ->get()
            ->map(function ($q) {
                $q->unread = Chat::Where(['thread' => $q->thread, 'read_status' => 0])->count();
                return $q;
            });
        if ($checkthread) {
            $data['thread'] = Chat::Where('thread', $thread)
                ->join('users as s', 's.id', '=', 'chats.sender_id')
                ->join('users as r', 'r.id', '=', 'chats.receiver_id')
                ->select('chats.*', 's.name as sender_name', 'r.name as receiver_name')
                ->where('chats.read_status', 0)
                ->get()
                ->map(function ($q) use ($id) {
                    if ($q->sender_id == $id) {
                        $q->rec = 0;
                    } elseif ($q->receiver_id == $id) {
                        $q->sender_name = $q->sender_name;
                        $q->receiver_name = $q->receiver_name;
                        $q->rec = 1;
                    }
                    $q->time = date('h:i a d M,y', strtotime($q->created_at));
                    return $q;
                });
            Chat::Where(['thread' => $thread, 'receiver_id' => $id])->update(['read_status' => 1]);
        }
        return json_encode($data);
    }

    public function sendmessage(Request $request)
    {
        $thread = $this->makethread(Auth::user()->id, $request->rid);

        $recivername = User::Where('id', $request->rid)->value('name');

        $chat = new Chat;

        $chat->sender_id = Auth::user()->id;
        $chat->receiver_id = $request->rid;
        $chat->message = $request->message;
        $chat->thread = $thread;

        $send = $chat->save();

        if ($send) {

            return json_encode(['status' => 'success', 'thread' => $thread, 'rid' => $request->rid, 'name' => $recivername]);
        }
    }

    public function users()
    {
        $data = User::Select('id', 'name')
            ->Where('id', '<>', Auth::user()->id)
            ->get()
            ->map(function ($q) {
                $q->thread = $this->makethread(Auth::user()->id, $q->id);
                return $q;
            });

        return json_encode($data);
    }

    private function makethread($id, $id2)
    {
        if ($id > $id2) {
            return $id2 . $id;
        } else {
            return $id . $id2;
        }
    }
}
