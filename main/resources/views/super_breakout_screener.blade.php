@extends('layouts.app')

@section('title', 'Page Title')

@section('sidebar')
    <p>This is appended to the master sidebar.</p>
@endsection

@section('content')
    <table class="table">
        <thead>
            <tr>
                <th>Sr No</th>
                <th>SYMBOL</th>
                <th>C.M.P.</th>
                <th>SMA 5</th>
                <th>SMA 10</th>
                <th>SMA 15</th>
                <th>SMA 20</th>
                <th>SMA 50</th>
                <th>SMA 100</th>
                <th>SMA 200</th>
                <th>DIFF%</th>
                <th>Chart</th>
            </tr>
        </thead>
        <tbody>
            <?php
                $count = 1;
            ?>
            @foreach($stockList as $key => $stock)
                <tr>
                    <td>{{ $count++ }}</td>
                    <td>{{ $stock['symbol'] }}</td>
                    <td>{{ $stock['cmp'] }}</td>
                    <td>{{ $stock['sma_5'] }}</td>
                    <td>{{ $stock['sma_10'] }}</td>
                    <td>{{ $stock['sma_15'] }}</td>
                    <td>{{ $stock['sma_20'] }}</td>
                    <td>{{ $stock['sma_50'] }}</td>
                    <td>{{ $stock['sma_100'] }}</td>
                    <td>{{ $stock['sma_200'] }}</td>
                    <td>{{ $stock['diffPercentage'] }}</td>
                    <td>
                        <a href="https://in.tradingview.com/chart/?symbol=NSE:{{ $stock['symbol'] }}" target="_blank">
                            Chart
                        </a>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endsection
