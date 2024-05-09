@extends('users.master.master');
@section('content')
        <h1>Visualizar Campanha</h1>
        <?php
        if (!empty($campaign)) {
            foreach ($campaign as $r) {
                ?>
                <h2><?=$r->title;?></h2>
                <p><?=$r->description;?></p>
                <p><?=$r->price;?></p>
                <p><?=$r->q_numbers;?></p>
                <p><?=$r->draw_date;?></p>
                <p><?=$r->status;?></p>
        <?php
            }
        }
        ?>
    @endsection

