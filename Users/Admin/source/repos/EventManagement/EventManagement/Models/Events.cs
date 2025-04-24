namespace EventManagement.Models
{
    public class Events
    {
        public Guid Id { get; set; }
        public required string EventName { get; set; }
        public required string Description { get; set; }
        public required string Location { get; set; }
        public required DateTime Date { get; set; }
        public required string Category { get; set; }
        public required string AttendeeCount { get; set; }
    }
}
