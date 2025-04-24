using System;
using System.Collections.Generic;
using System.ComponentModel.DataAnnotations.Schema;
using System.Linq;
using System.Threading.Tasks;
using Microsoft.AspNetCore.Identity;
using Microsoft.EntityFrameworkCore;
using Microsoft.Extensions.Logging;

namespace EventManagement.Areas.Identity.Data;

// Add profile data for application users by adding properties to the EventUser class
public class EventUser : IdentityUser
{
    [PersonalData]
    [Column(TypeName = "nvarchar(100)")]
    public required string FullName { get; set; }

    [PersonalData]
    [Column(TypeName = "nvarchar(100)")]
    public required string Organization { get; set; }
    //public DbSet<Events> Events { get; set; }
}

