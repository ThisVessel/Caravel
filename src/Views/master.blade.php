<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Caravel Admin - @yield('title')</title>

    <!-- Stylesheets -->
    @section('stylesheets')
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.2/css/bootstrap.min.css">
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css">
        @include('caravel::shame.css')
    @show

</head>
<body>
    <div class="container">

        <!-- Page Header -->
        <div class="page-header">
            <h1 class="display-4">Caravel Admin</h1>
            <p class="lead">Powered by This Vessel</p>
        </div>

        <!-- Page Wrapper -->
        <div class="row">

            <!-- Navigation -->
            <div class="col-md-3">
                {{-- Temp! Messing with collapsable menu for mobile.
                <ul id="navigation-toggle" class="nav nav-pills nav-stacked">
                    <li class="nav-item">
                        <a class="nav-link active" data-toggle="collapse" href="#navigation" aria-expanded="false" aria-controls="navigation">
                            <i class="fa fa-bars"></i> Navigation
                        </a>
                    </li>
                </ul>
                <ul id="navigation" class="nav nav-pills nav-stacked">
                --}}
                <ul class="nav nav-pills nav-stacked">
                    <li class="nav-item">
                        <a href="{{ route('caravel::dashboard') }}" class="nav-link {{ !isset($resource) ? 'active' : '' }}">Dashboard</a>
                    </li>
                    @inject('drawbridge', '\ThisVessel\Caravel\Helpers\Drawbridge')
                    @foreach (config('caravel.resources') as $uri => $model)
                        @if ($drawbridge::allows('manage', new $model))
                            <li class="nav-item">
                                <a href="{{ route('caravel::' . $uri . '.index') }}" class="nav-link {{ isset($resource) && $resource == $uri ? 'active' : '' }}">{{ ucwords(implode(' ', explode('-', $uri))) }}</a>
                            </li>
                        @endif
                    @endforeach
                    @if (config('caravel.logout'))
                        <li class="nav-item">
                            @if (substr(config('caravel.logout'), 0, 1) == '/')
                                <a href="{{ config('caravel.logout') }}" class="nav-link m-t-1 logout"><i class="fa fa-sign-out">&nbsp;&nbsp;Logout</i></a>
                            @else
                                <a href="/{{ config('caravel.logout') }}" class="nav-link m-t-1 logout"><i class="fa fa-sign-out">&nbsp;&nbsp;Logout</i></a>
                            @endif
                        </li>
                    @endif
                </ul>
            </div>

            <!-- Container -->
            <div class="col-md-9">
                @yield('container')
            </div>

        </div>
    </div>

    <!-- Scripts -->
    @section('scripts')
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/2.1.4/jquery.js"></script>
        <script src="//maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.2/js/bootstrap.min.js"></script>
    @show

</body>
</html>
