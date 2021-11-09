<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>{{env('APP_NAME') }} Messaging</title>
    <link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:400,600" rel="stylesheet">

    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/meyer-reset/2.0/reset.min.css">
    <link rel="stylesheet" href="{{asset('livechat/css/style.css')}}">

</head>

<body>
    <!-- partial:index.partial.html -->
    <div class="wrapper">
        <div class="container">
            <div class="left">
                <div class="top">
                    <input type="text" placeholder="Search" />
                    <a href="javascript:;" class="search" id="createmsg"></a>
                </div>
                <ul class="people" id="chatlist">
                    <!--  -->
                </ul>
            </div>
            <div class="right">
                <div class="top">
                    <span>To: <span class="name sendername"></span></span>
                </div>
                <div class="chat active-chat">
                    <!-- <div class="conversation-start">
                        <span>Today, 6:48 AM</span>
                    </div> -->
                    <div class="chat-show"></div>

                </div>

                <div class="write">
                    <form method="post" id="messagebox">
                        @csrf
                        <a href="javascript:;" class="write-link attach"></a>
                        <input type="text" id="message" name="message" />
                        <input type="hidden" id="thread" name="thread" value="0">
                        <input type="hidden" id="rid" name="rid">
                        <a href="javascript:;" class="write-link smiley"></a>
                        <a href="javascript:;" class="write-link send" onclick="$('#messagebox').submit();"></a>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- partial -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="{{asset('livechat/js/script.js')}}"></script>

</body>

</html>