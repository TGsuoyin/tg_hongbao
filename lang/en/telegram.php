<?php
return [
    //用户相关
    'notregistered'=>'The user is not registered, please register first! Command: /register',
    'userbanned'=>'User was banned',
    'balance'=>'balance',
    'nobalance'=>'Insufficient user balance',
    //今日报表
    'todayprofit'=>'today profit',
    'todayrecharge'=>'today recharge',
    'todaywithdraw'=>'today withdraw',
    'todaysendamount'=>'today amount',
    'expenditure'=>'expenditure',
    'awarding'=>'awarding',
    'bagincome'=>'bag income',
    'thunderlose'=>'thunder lose',
    'inviterebate'=>'invite rebate',
    'shareprofit'=>'share profit',
    //昨日报表
    'yesterdayprofit'=>'yesterday profit',


    //推广查询
    'todayinvite'=>'Today invitations',
    'monthinvite'=>'Month invitations',
    'totalinvite'=>'Total invitations',
    'lastteninvitations'=>'Show the last ten invitations',

    //发包
    'commanderror_integer'=>'Command error, please enter an integer',
    'commanderror_thundernum'=>'Command error, the thunder number should be between 0 and 9',
    'error_lessthan'=>'The amount of red envelope cannot be less than :minAmount U',
    'error_greaterthan'=>'The amount of red envelope cannot be greater than :maxAmount U',
    'registersuccess'=>'Registration success',
    'registerfailed'=>'Registration failed',
    'withdrawfailed'=>'Withdraw failed',
    'rechargefailed'=>'Recharge failed',
    'userregistered'=>'User registered',
    'failedtosend'=>'Failed to send',
    'nopicture'=>'Please set the picture in the background',
    'firstbtntext'=>'🧧Grab [:luckyTotal/0] Total :amount U 💥 :mine',
    'sendcaption'=>'Sent a :amount U red envelope, come and grab it!',
    'insufficientbalance'=>'Your balance is insufficient to issue the package',
    'insufficientbalancetips'=>'Insufficient balance, balance required >=:lowestAmount Or the status is abnormal~',


    //福利包
    'welfarelimit'=>'The number of welfare red envelopes must be greater than 2 and less than 100',
    'welfare'=>'welfare',
    'welfaretoomany'=>'Too many quantities',
    'welfarefirstbtntext'=>'🧧【Welfare】 [:num/0] Total :amount U ',
    'welfaresendcaption'=>'Sent a :amount U Welfare Red envelope, come and grab it!',

    //抢包
    'grab_self'=>'Unable to grab the red envelopes given by yourself',
    'receivedonce'=>'You have received the red envelope, the amount is :amount U',
    'nodata'=>'No data',
    'collectedall'=>'All red envelopes have been collected',
    'hasthunderanswer'=>'Hit by mine, receive :redAmount U, lose :loseMoney U',
    'nothunderanswer'=>'💵You grabbed  :redAmount U',
    'welfareanswer'=>'Congratulations, you grabbed :redAmount U',
    'welfare_envelopes'=>'welfare envelopes',
    'total'=>'Total',
    'thunder'=>'💥 ',
    'envelopes'=>'envelopes',
    'envelopes_collect'=>'🧧Grab',
    'welfare_collect'=>'🧧[Welfare] Come and grab it',
    'collect_over'=>"[ <code>:sender_name</code> ] red envelope has been collected！\n
🧧Amount：:luckyAmount U
🛎Rate：:lose_rate
💥Thunder：:thunder\n
--------Details--------\n
:details
💹 Profit： :loseMoneyTotal
💹 Amount：-:luckyAmount
💹 Received：:profitTxt",
    'welfare_collect_over'=>"[ <code>:sender_name</code> ] welfare red envelope has been collected！\n
🧧Amount：:luckyAmount U
\n
--------Details--------\n
:details
💹 Amount：-:luckyAmount
",
    'leopard_reward'=>"🎉🎉[  :userName  ] Grabbing the Threes Full :redAmount Reward :leopardReward has been received.",
    'straight_reward'=>"🎉🎉[  :userName  ] Grabbing the Straight :redAmount Reward :straightReward has been received.",
    'jackpot_reward'=>"🌟Congratulations! Won the jackpot🌟\n Winning amount <b>:rewardAmount</b>\n Congratulations to the following winning users:\n\n",
    'jackpot_bonus_send'=>"\n The bonus has been automatically distributed to the account, please check~ \n",
    'jackpot_cumulative'=>"🌟 JackPot prize pool cumulative amount: :amount U 🌟",


    //过期
    'valid_returned'=>'(Returned)',
    'valid_caption'=>"[ <code>:sender_name</code> ]’s red envelope has expired！\n
🧧Amount：:luckyAmount U
🛎Rate：:lose_rate
💥Thunder：:thunder\n
--------Details--------\n
:details
💹 Profit： :loseMoneyTotal
💹 Amount：-:luckyAmount
💹 Received：:qiangTotal
💹 Surplus：:shengyuText
💹 Actually Received：:profitTxt
Tips：[ <code>:sender_name</code> ]’s red envelope has expired！",
    'welfare_valid_caption'=>"[ <code>:sender_name</code> ]’s red envelope has expired！\n
🧧Amount：:luckyAmount U

--------Details--------\n
:details
💹 Amount：-:luckyAmount
💹 Received：:qiangTotal
💹 Surplus：:shengyuText
Tips：[ <code>:sender_name</code> ]’s red envelope has expired！",

    //上分下分
    'withdraw'=>'withdraw',
    'recharge'=>'recharge',
    'withdrawerr'=>'Amount error',
    'rechargeerr'=>'Amount error',
    "withdrawmsg"=>'✅ Withdraw <b>:amount</b> Success
🔹Username:  <code>:username</code>
🔹User ID: <code>:tgId</code>
🔹balance: <b>:balance</b> U',
    "rechargemsg"=>'✅ Recharge <b>:amount</b> Success
🔹Username: <code>:username</code>
🔹User ID: <code>:tgId</code>
🔹balance: <b>:balance</b> U',

    //按钮
    'btn_service'=>'Service',
    'btn_recharge'=>'Recharge',
    'btn_rule'=>'Rules',
    'btn_balance'=>'Balance',
    'btn_promotion'=>'Invitations',
    'btn_report'=>'Today report',
    'btn_invitelink'=>'Invitelink',
    'yesterday_report'=>'Yesterday report',
    'team_report'=>'team report',

    //邀请
    'invite_err1'=>'The user is not registered. Please join the group first. After joining the group, you will be automatically registered.',
    'invite_err2'=>'Invitation link creation failed! Please contact the administrator',
    'invite_link'=>"[ :username ] Your exclusive link is  :invite_link
(Users who join will automatically become your subordinate users)",

    'start_msg'=>"👏 Welcome to TG Red Packet Thunder Game , Your ID: :userId",

    'photo'=>'photo',
    'groupinfo'=>'groupinfo',
    'group_id'=>'Group ID',
    'user_id'=>'User ID',
];
