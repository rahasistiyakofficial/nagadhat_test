<!DOCTYPE html>
<html>

<head>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>

<body>
    <div class="container mt-4">
        <h1 class="mb-4">User Search History</h1>
        <div class="row">
            <div class="col-6">
                <div class="card">
                    <div class="card-body">
                        <h2 class="card-title">Filters:</h2>

                        <form id="searchFilters">
                            <div class="row">
                                <div class="col-6">
                                    <div class="mb-3">
                                        <h3>All Keywords:</h3>
                                        @foreach ($keywords as $keyword)
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="keywords[]"
                                                    value="{{ $keyword->id }}" id="keyword{{ $keyword->id }}">
                                                <label class="form-check-label" for="keyword{{ $keyword->id }}">
                                                    {{ $keyword->keyword }} ({{ $keyword->count }} times found)
                                                </label>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="mb-3">
                                        <h3>All Users:</h3>
                                        @foreach ($users as $user)
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="users[]"
                                                    value="{{ $user->id }}" id="user{{ $user->id }}">
                                                <label class="form-check-label" for="user{{ $user->id }}">
                                                    {{ $user->name }}
                                                </label>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-6">
                                    <div class="mb-3">
                                        <h3>Time Range:</h3>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="yesterday"
                                                id="yesterday">
                                            <label class="form-check-label" for="yesterday">See data from
                                                yesterday</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="last_week"
                                                id="last_week">
                                            <label class="form-check-label" for="last_week">See data from last
                                                week</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="last_month"
                                                id="last_month">
                                            <label class="form-check-label" for="last_month">See data from last
                                                month</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-6">

                                    <div class="mb-3">
                                        <h3>Select Date:</h3>
                                        <input class="form-control" type="date" name="start_date" id="start_date"
                                            placeholder="Enter start date">
                                        <input class="form-control" type="date" name="end_date" id="end_date"
                                            placeholder="Enter end date">
                                    </div>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary">Apply Filters</button>
                            <input type="reset" class="btn btn-secondary" value="Reset Form">
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-6">
                <div id="searchResults">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Search Keyword</th>
                                <th>Search Time</th>
                                <th>Search Results</th>
                            </tr>
                        </thead>
                        <tbody>

                        </tbody>
                    </table>
                </div>
            </div>
        </div>


    </div>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#searchFilters').on('submit', function(e) {
                e.preventDefault();
                const startDate = $('#start_date').val();
                const endDate = $('#end_date').val();
                const yesterdayCheckbox = $('#yesterday');
                const lastWeekCheckbox = $('#last_week');
                const lastMonthCheckbox = $('#last_month');
                const startDateInput = $('#start_date');
                const endDateInput = $('#end_date');

                function toggleDateInputs() {
                    const isTimeRangeSelected = yesterdayCheckbox.prop('checked') ||
                        lastWeekCheckbox.prop('checked') ||
                        lastMonthCheckbox.prop('checked');
                       
                    if (isTimeRangeSelected) {
                        startDateInput.val('');
                        endDateInput.val('');
                    }
                    startDateInput.prop('disabled', isTimeRangeSelected);
                    endDateInput.prop('disabled', isTimeRangeSelected);
                }

                toggleDateInputs();

                yesterdayCheckbox.on('change', toggleDateInputs);
                lastWeekCheckbox.on('change', toggleDateInputs);
                lastMonthCheckbox.on('change', toggleDateInputs);

                if (startDate > endDate) {
                    if (!endDate) {
                        alert('End date Required.');

                    } else {
                        alert('End date cannot be smaller than the start date.');

                    }
                    return;
                }
                const filters = $(this).serialize();

                $.ajax({
                    url: '{{ route('search-history') }}',
                    method: 'GET',
                    data: filters,
                    success: function(data) {
                        $('#searchResults tbody').empty();
                        console.log(data);
                        if (data.searchHistory.length > 0) {
                            data.searchHistory.forEach(function(history) {
                                var newRow = $('<tr>');
                                newRow.append($('<td>').text(history.search_keyword));
                                newRow.append($('<td>').text(history.search_time));
                                newRow.append($('<td>').text(history.search_results));

                                $('#searchResults tbody').append(newRow);
                            });
                        } else {
                            var noDataRow = $('<tr>');
                            noDataRow.append($('<td colspan="3" style="color: red;">').text(
                                'No data'));
                            $('#searchResults tbody').append(noDataRow);
                        }
                    },
                    error: function(xhr, status, error) {}
                });


            });
        });
    </script>
</body>

</html>
