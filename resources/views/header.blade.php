<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8"/>
        
        <script type="text/javascript" src="{{ asset('js/jquery-1.12.0.min.js') }}" ></script>
        <script type="text/javascript" src="{{ asset('bootstrap/js/bootstrap.min.js') }}"></script>
        <script type="text/javascript" src="{{ asset('datepicker/js/bootstrap-datepicker.min.js') }}" type="text/javascript" ></script>
        <script type="text/javascript" src="{{ asset('bootstrapselect/dist/js/bootstrap-select.min.js') }}" type="text/javascript" ></script>
        <script type="text/javascript" src="{{ asset('js/scripts.js') }}"></script>
        
        
        <link href="{{ asset('datepicker/css/bootstrap-datepicker.min.css') }}" rel="stylesheet">
        <link href="{{ asset('bootstrapselect/dist/css/bootstrap-select.min.css') }}" rel="stylesheet">
        <link href="{{ asset('bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">
        <title>CSM</title>
    </head>
    <body>
        <nav class="navbar navbar-inverse navbar-fixed-top">
            <div class="container">
                <div class="navbar-header">
                    <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
                        <span class="sr-only">Toggle navigation</span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>
                    <a class="navbar-brand" href="http://reports.dev/">CSM</a>
                </div>
                <div id="navbar" class="navbar-collapse collapse">
                    <ul class="nav navbar-nav">
                        @if(Auth::check())
                        <li><a href="<?php echo url('manage_judges'); ?>">Judges</a></li>
                        <li><a href="<?php echo url('manage_courts'); ?>">Courts</a></li> 
                        <li><a href="<?php echo url('manage_judge_court'); ?>">Judge Courts</a></li> 
                        <li><a href="<?php echo url('manage_monthly_report'); ?>">Monthly Report</a></li> 
                        <li><a href="<?php echo url('manage_judgement'); ?>">Judgements</a></li> 
                        <li><a href="<?php echo url('manage_court_fields'); ?>">Court Fields</a></li> 
                        <li><a href="<?php echo url('manage_configs'); ?>">Configs</a></li> 
                        @endif
                    </ul>
                    <ul class="nav navbar-nav navbar-right">
                        <?php
                        if(Auth::check())
                        {
                            ?>
                            <li><a href="{{ url('/logout') }}"><span class="glyphicon glyphicon-log-out"></span> Logout</a></li>
                            <?php
                        }
                        else
                        {
                            ?>
                            <li><a href="{{ url('/login') }}"><span class="glyphicon glyphicon-log-in"></span> Login</a></li>
                            <li><a href="{{ url('/register') }}"><span class="glyphicon glyphicon-user"></span> Register</a></li>
                            <?php
                        }
                        ?>
                    </ul>
                </div>
            </div>
        </nav>
        <div class="jumbotron">
            <div class="container">
                <h1><?php echo $page_title; ?></h1>
            </div>
        </div>

        <div class="container">
