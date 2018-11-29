**Cài đặt**

1. Cài đặt redis server

```
sudo apt update
sudo apt install redis-server
```
Cấu hình redis server

``
sudo nano /etc/redis/redis.conf
``

Tìm `supervised no` thay thành `supervised systemd`

Khởi động lại `sudo systemctl restart redis.service`

Kiểm tra redis server hoạt động chưa `sudo systemctl status redis`

Nếu không muốn mở redis thủ công `sudo systemctl disable redis`

Test:

```
redis-cli
ping
```
=>pong
=>ok

Đổi cổng

`redis-server --port number_port`

2. Cài đặt Redis vào laravel

Vào project chạy

`composer require predis/predis`

Cấu hình:

Vào đường dẫn `config/database.php`

```
'redis' => [

    'client' => 'predis',

    'default' => [
        'host' => env('REDIS_HOST', 'localhost'),
        'password' => env('REDIS_PASSWORD', null),
        'port' => env('REDIS_PORT', 6379),
        'database' => 0,
    ],

]
```

Chủ yếu cấu hình port với pass redis server nếu có


**Tương tác với Redis**

Để tương tác với Redis sử dụng Redis facade

Ví dụ:
Set key name, value your_name

`Redis::set('name', 'Taylor');`

Get name by key

`Redis::get('name');`

Danh sách:

```
Redis::rpush('mylist', 'hello');
Redis::rpush('mylist', 'hello1');
$mylist = Redis::lrange('mylist', 0, 1);
var_dump($mylist);
```
Ngoài sử dụng redis facade có thể sử dụng command method

`$values = Redis::command('mylist', ['hello', hello2, hello3]);`

Nhận kết nối redis

`$redis = Redis::connection();` hoặc  `$redis = Redis::connection('my-connection');` nếu có nhiều kết nối

Pipelining nên được sử dụng khi bạn cần gửi nhiều lệnh đến máy chủ trong một thao tác. Các  pipelinephương pháp chấp nhận một đối số: một Closuretiếp nhận một trường hợp Redis. Bạn có thể phát hành tất cả các lệnh của bạn đến phiên bản Redis này và tất cả chúng sẽ được thực hiện trong một thao tác đơn lẻ:

```
Redis::pipeline(function ($pipe) {
    for ($i = 0; $i < 1000; $i++) {
        $pipe->set("key:$i", $i);
    }
});
```
Publish and Subscribe

 Các lệnh Redis này cho phép bạn nghe tin nhắn trên "kênh" đã cho. Bạn có thể xuất bản tin nhắn tới kênh từ ứng dụng khác hoặc thậm chí sử dụng ngôn ngữ lập trình khác, cho phép liên lạc dễ dàng giữa các ứng dụng và quy trình.
 
 Trước tiên, hãy thiết lập trình nghe kênh bằng subscribe method. Chúng ta sẽ gọi phương thức này trong một lệnh Artisan vì việc gọi subscribe method bắt đầu một quá trình chạy dài:

```
<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Redis;

class RedisSubscribe extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'redis:subscribe';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Subscribe to a Redis channel';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        Redis::subscribe(['test-channel'], function ($message) {
            echo $message;
        });
    }
}
```

publish messages tới kênh nào đó sử dụng publish method:

```
Route::get('publish', function () {
       // Route logic...
   
  Redis::publish('my_channel', json_encode(['name' => 'myname']));
})
```
Sử dụng psubscribe để đăng ký kênh đại diện để bắt tất cả message trên các kênh

```
Redis::psubscribe(['*'], function ($message, $channel) {
    echo $message;
});

Redis::psubscribe(['users.*'], function ($message, $channel) {
    echo $message;
});
```

**Tạo ứng dụng chat đơn giản kết hợp Nodejs và socketio**

1. Tạo folder node_server trong project laravel để tạo server node
Vào folder mới tạo chạy lệnh: 
`npm install express redis socket.io --save`

để cài đặt các gói cần thiết

Tạo file server.js với nội dung sau

```
var app = require('express')();
var server = require('http').Server(app);
var io = require('socket.io')(server);
var redis = require('redis');

server.listen(8890);
io.on('connection', function (socket) {

    console.log("new client connected");
    var redisClient = redis.createClient();
    redisClient.subscribe('message');

    redisClient.on("message", function(channel, message) {
        console.log("new message in queue "+ message + "channel");
        socket.emit(channel, message);
    });

    socket.on('disconnect', function() {
        console.log('disconnect');
        redisClient.quit();
    });

});
```

Chúng ta khởi động một http server ở cổng 8090 và gắn nó với Socket.IO server. Sau đó chúng ta tạo ra một Redis client, và mỗi khi có một socket client kết nối thì chúng ta sẽ subscribe một channel trong Redis.

Khi có tin nhắn được push vào kênh này, thì redisClient sẽ xảy ra sự kiện “message”, và socket sẽ emit nó đến với các client đang kết nối.

Để khởi động server node, từ trong cửa sổ lệnh, chúng ta gõ lệnh sau:

`node server.js`

2. Tạo controller mới tên socketController với nội dung sau

```
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Messages;
class socketController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $all = DB::table('messages')->select('messages.id','name','id_user','content')->leftJoin('users','users.id','=','messages.id_user')->orderBy('messages.id','asc')->get();
        return view('writemessage',compact('all'));
    }
    public function sendMessage(Request $request){
        Messages::create(['id_user'=>Auth::id(),'content'=>$request->get('message')]);
        $redis = Redis::connection();
        $redis->publish('message', json_encode(['name'=>Auth::user()->name,'content'=>$request->get('message')]));
        return redirect()->route('chat');
    }
}

```
method index() dùng để viết tin nhắn + hiện thị

method sendMessage() Nhận request lưu db + đẩy tin nhắn ra kênh `message`
, sau khi đẩy ra kênh node server sẽ đẩy ngược lại client

Tạo model Messages.php

```
<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Messages extends Model
{
    protected $table = 'messages';
    protected $fillable = ['id_user','content'];
}

```

Phần view:

writemessage.blade.php


```
@extends('layouts.app')

@section('content')
    <script src="https://code.jquery.com/jquery-3.2.1.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/socket.io/2.0.1/socket.io.js"></script>

    <div class="container">
        <div class="row">
            <div class="col-4 col-offset-2" >
                <ul id="messages">
                @foreach($all as $value)
                    <li>{{ $value->name.': '.$value->content }}</li>
                    @endforeach
                </ul>
                {{--<div id="messages" ></div>--}}
                <div class="panel panel-default">
                    <div class="panel-heading">Send message</div>
                    <form action="sendmessage" method="POST">
                        @csrf
                        <input type="text" name="message" >
                        <input type="submit" value="send">
                    </form>
                </div>
            </div>
        </div>
    </div>
    <script>
        var socket = io.connect('http://localhost:8890');
        socket.on('message', function (data) {
            console.log(data);
            var obj = JSON.parse(data);
            $( "#messages" ).append( "<li>"+obj.name+': '+obj.content+"</li>" );
        });
    </script>

@endsection
```

Tạo 2 route sau:

```
Route::get('chat', 'socketController@index')->name('chat');
Route::post('sendmessage', 'socketController@sendMessage');
```


