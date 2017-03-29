@extends('layouts.testenv_navframe')
@section('page_heading','Post Responses')
@section('section')

        <!-- /.row -->
        <div class="col-sm-12">
        <!-- /.row -->

        <div class="row"> <!-- START Table Row -->
            <div class="col-lg-12">
                <!-- Find a Partner -->
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h3 class="panel-title">Get Pending Requests</h3>
                    </div>
            
                    <div class="panel-body">

                        @if (empty($partners))
                            <div class="alert alert-warning">
                                You are not configured for Payout services or you do not have any active Send partners.
                            </div>
                        @else 
                            <form role="form" method="POST" action="/testenv/gettxnposts">
                            {{ csrf_field() }}
                            <fieldset>
                                <div class="row">
                                    <div class="form-group col-lg-4">
                                        <label>From Remitter:</label>
                                        <select class="form-control" name="from_remitter_id">
                                            <option value="-1">Select A Partner</option> 
                                            @foreach ($partners as $partner)
                                                <option value="{{ $partner->partner_id }}" @if (old('from_remitter_id') == $partner->partner_id) selected="selected" @endif>{{ $partner->partner_id }}:{{ $partner->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group col-lg-3">
                                        <label>Request Type:</label>
                                        <select class="form-control" name="txnpost_type">
                                            <option value="PENDING_ACK" @if (old('txnpost_type') == "PENDING_ACK") selected="selected" @endif>PENDING ACK</option> 
                                            <option value="ALL_ACKED" @if (old('txnpost_type') == "ALL_ACKED") selected="selected" @endif>All Acknowledged</option> 
                                            <option value="REQ_NEW" @if (old('txnpost_type') == "REQ_NEW") selected="selected" @endif>REQ_NEW</option> 
                                            <option value="REQ_MOD" @if (old('txnpost_type') == "REQ_MOD") selected="selected" @endif>REQ_MOD</option> 
                                            <option value="REQ_CAN" @if (old('txnpost_type') == "REQ_CAN") selected="selected" @endif>REQ_CAN</option> 
                                        </select>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="form-group col-lg-12">
                                        <button type="submit" name="submit" value="get" class="btn btn-success">Get Requests</button>
                                    </div>
                                </div>
                            </fieldset>
                            </form>
                        @endif
                    </div>
                </div> <!-- Panel -->
            </div> <!-- /.col-lg-4 -->
        </div> <!-- END Table Row -->
        
        <div class="row"> <!-- START Error/Success Messages Row -->
            <div class="col-lg-12">
                    {{-- Display errors generated by the validator --}}
                    @if (count($errors) > 0)
                        <div id="div-errors" class="alert alert-danger">
                            <div class="list-group" style="margin-bottom:0px;">
                                @foreach ($errors->all() as $error)
                                <span class="list-group-item">{{ $error }}</span>
                                @endforeach
                            </div>
                        </div>
                    @endif
                    @if (session()->has('success'))
                        <div class="alert alert-success">
                            {{ session()->get('success') }}
                        </div>
                    @endif
            </div>
        </div>

        <div class="row"> <!-- START Table Row -->
            <div class="col-lg-12">

                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h3 class="panel-title">Pending Requests ({{ count($txnposts) }}) </h3>
                    </div>
            
                    <div class="panel-body visible-xs-block visible-sm-block">
                        <div class="alert alert-info" role="alert">Data table only visible on wider screen formats.</div>
                    </div>

                    <!-- Show transactions pending processing for selected partner -->
                    <div class="panel-body visible-md-block visible-lg-block">
                    @if (count($txnposts) > 0)
                        <form role="form" method="POST" action="/testenv/posttxnresponses">
                        {{ csrf_field() }}
                        <div class="form-group">
                        <table class="table table-bordered" style="width:100%;">
                            <thead>
                            <tr>
                                @if (old('txnpost_type') == "-1")
                                    <div class="alert alert-warning">
                                        No actions possible when viewing all request types. Select a specific request type to enable response actions.
                                    </div>
                                @elseif (old('txnpost_type') == "PENDING_ACK")
                                    <div class="form-group">
                                        <button type="submit" name="submit" value="ACK_REQ" class="btn btn-success">ACK_REQ</button>
                                    </div>
                                @else
                                    <div class="form-group">
                                        @if (old('txnpost_type') == "REQ_NEW" || old('txnpost_type') == "REQ_MOD")
                                            <button type="submit" name="submit" value="CNF_PD" class="btn btn-success">CNF_PD</button>
                                        @elseif (old('txnpost_type') == "REQ_CAN")
                                            <button type="submit" name="submit" value="CNF_CAN" class="btn btn-success">CNF_CAN</button>
                                        @endif
                                        <button type="submit" name="submit" value="CNF_PD" class="btn btn-danger">REQ_REJ</button>
                                @endif
                                    </div>
                            </tr>
                            <tr>
                                @unless (old('txnpost_type') == "-1")
                                <th>
                                    <input name="select_all" type="checkbox" value="no">
                                </th>
                                @endunless
                                <th>UUID</th>
                                <th>Origin UUID</th>
                                <th>From</th>
                                <th>To</th>
                                <th>Type</th>
                                <th>Posted On</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach ($txnposts as $txnpost)
                                <tr>
                                    @unless (old('txnpost_type') == "-1")
                                    <td>
                                        <input value={{ $txnpost->uuid }} name="selected_uuids[]" type="checkbox">
                                    </td>
                                    @endunless
                                    <td>
                                        {{ $txnpost->uuid }}
                                    </td>
                                    <td>
                                        {{ $txnpost->origin_uuid }}
                                    </td>
                                    <td>
                                        {{ $txnpost->from_rmtr_id }}
                                    </td>
                                    <td>
                                        {{ $txnpost->to_rmtr_id }}
                                    </td>
                                    <td>
                                        {{ $txnpost->type }}
                                    </td>
                                    <td>
                                        {{ $txnpost->posted_on }}
                                    </td>
                                </tr>
                            @endforeach
                        </table>
                        </div> <!-- END form-group -->
                        </form>
                    @else
                        <div id="div-requests" class="alert alert-warning">
                        No pending requests.
                        </div>
                    @endif
                    </div> <!-- END panel body -->
                </div>
            </div>
        </div>

        @stop
