namespace EventManagement.Models
{
    public class MonthlyReportView
    {
            public int Year { get; set; }
            public int Month { get; set; }
            public int EventCount { get; set; }
            public long TotalAttendees { get; set; }
            public string MonthName => new DateTime(Year, Month, 1).ToString("MMMM yyyy");

            internal object OrderByDescending(Func<object, object> value)
            {
                throw new NotImplementedException();
            }
        }
}
