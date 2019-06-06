@extends('layouts.app')

@section('asset_includes')
<link href="{{ asset('css/faq.css') }}" rel="stylesheet">
@endsection

@section('title', 'FAQ - Eventually')

@section('content')
    <div id="background_wave"></div>
    
    <div id="page-card" class="container card-container font-content faq-container">
        <header>
            <h1>FAQ - Frequently Asked Questions</h1>
            <div class="mobile-wave" id="background_wave"></div>
        </header>
        <div class="qa-container">
            <div class="qa-item">
                <div class="question" data-toggle="collapse" data-target="#answer_1">
                    Q: How many events can I organize at the same time using this application?
                </div>
                <div class="collapse answer" id="answer_1">
                    <strong>A:</strong> Using Eventually, you may organize any number of events at the same time at any given time, free
                    of charge.
                </div>
            </div>
            <div class="qa-item">
                <div class="question" data-toggle="collapse" data-target="#answer_2">
                    Q: I don't want to organize an event by myself. Can I get help from other users?
                </div>
                <div class="collapse answer" id="answer_2">
                    <strong>A:</strong> Yes. Eventually offers a collaborative way to manage your events. You need only to search for
                    other users in your event's page and invite them to assist you in the organization process. Once
                    they accept your invitation, they will be able to emit vouchers, check-in attendees, create
                    annoucements and edit the event's data.
                </div>
            </div>
            <div class="qa-item">
                <div class="question" data-toggle="collapse" data-target="#answer_3">
                    Q: I want to contact an administrator. How may I do so?
                </div>
                <div class="collapse answer" id="answer_3">
                    <strong>A:</strong> In order to contact one of the administrators, you need only click the "Submit an Issue" button
                    on your navigation bar, at the top of the page. The most common usage of this tool is for reporting
                    an event, reporting another user's behavior or asking questions about the application.
                </div>
            </div>
            <div class="qa-item">
                <div class="question" data-toggle="collapse" data-target="#answer_4">
                    Q: I want to communicate with the event organizers. How may I do so?
                </div>
                <div class="collapse answer" id="answer_4">
                    <strong>A:</strong> In order to contact the event organizers, you can visit the event's page and scroll down to the
                    "Discussion" section. In this section, you are able to create a post asking a question and observe
                    other matters related to the event.
                </div>
            </div>
            <div class="qa-item">
                <div class="question" data-toggle="collapse" data-target="#answer_5">
                    Q: How many events may I attend at the same time?
                </div>
                <div class="collapse answer" id="answer_5">
                    <strong>A:</strong> As many events as you want. Eventually allows you to take part in multiple events at the same time, no restrictions.
                </div>
            </div>
            <div class="qa-item">
                <div class="question" data-toggle="collapse" data-target="#answer_6">
                    Q: I want to easily follow an event's activity. How may I do so?
                </div>
                <div class="collapse answer" id="answer_6">
                    <strong>A:</strong> In order to follow all the event's activity, you need only to mark the event as <strong>Favorite</strong>. To do so, just access the event's page and click the <strong>Star</strong> icon on the top, near the event's title. 
                </div>
            </div>
        </div>
    </div>
@endsection