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
                    A: Using Eventually, you may organize any number of events at the same time at any given time, free
                    of charge.
                </div>
            </div>
            <div class="qa-item">
                <div class="question" data-toggle="collapse" data-target="#answer_2">
                    Q: I don't want to organize an event by myself. Can I get help from other users?
                </div>
                <div class="collapse answer" id="answer_2">
                    A: Yes. Eventually offers a collaborative way to manage your events. You need only to search for
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
                    A: In order to contact one of the administrators, you need only click the "Submit an Issue" button
                    on your navigation bar, at the top of the page. The most common usage of this tool is for reporting
                    an event, reporting another user's behavior or asking questions about the application.
                </div>
            </div>
            <div class="qa-item">
                <div class="question" data-toggle="collapse" data-target="#answer_4">
                    Q: I want to communicate with the event organizers. How may I do so?
                </div>
                <div class="collapse answer" id="answer_4">
                    A: In order to contact the event organizers, you can visit the event's page and scroll down to the
                    "Discussion" section. In this section, you are able to create a post asking a question and observe
                    other matters related to the event.
                </div>
            </div>
            <div class="qa-item">
                <div class="question" data-toggle="collapse" data-target="#answer_5">
                    Q: I'm used to using Google™ Calendar for managing my appointments. Can I integrate my events with
                    it?
                </div>
                <div class="collapse answer" id="answer_5">
                    A: Yes. Eventually allows the possibility of exporting an event to your own Google™ Calendar. To do
                    so, you need only to access the event's page and click the "Export to Google Calender" button.
                </div>
            </div>
        </div>
    </div>
@endsection