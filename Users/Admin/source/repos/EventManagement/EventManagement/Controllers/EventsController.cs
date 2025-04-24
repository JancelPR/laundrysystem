using System;
using System.Collections.Generic;
using System.Linq;
using System.Threading.Tasks;
using Microsoft.AspNetCore.Mvc;
using Microsoft.AspNetCore.Mvc.Rendering;
using Microsoft.EntityFrameworkCore;
using EventManagement.Data;
using EventManagement.Models;
using Microsoft.AspNetCore.Authorization;

namespace EventManagement.Controllers
{
    public class EventsController : Controller
    {
        private readonly AuthDbContext _context;

        public EventsController(AuthDbContext context)
        {
            _context = context;
        }
        [Authorize]
        // GET: Events
        public async Task<IActionResult> Index()
        {
            return View(await _context.Events.ToListAsync());
        }

        // GET: Events/Details/5
        public async Task<IActionResult> Details(Guid? id)
        {
            if (id == null)
            {
                return NotFound();
            }

            var events = await _context.Events
                .FirstOrDefaultAsync(m => m.Id == id);
            if (events == null)
            {
                return NotFound();
            }

            return View(events);
        }

        // GET: Events/Create
        public IActionResult Create()
        {
            return View();
        }

        // POST: Events/Create
        // To protect from overposting attacks, enable the specific properties you want to bind to.
        // For more details, see http://go.microsoft.com/fwlink/?LinkId=317598.
        [HttpPost]
        [ValidateAntiForgeryToken]
        public async Task<IActionResult> Create([Bind("Id,EventName,Description,Location,Date,Category,AttendeeCount")] Events events)
        {
            if (ModelState.IsValid)
            {
                events.Id = Guid.NewGuid();
                _context.Add(events);
                await _context.SaveChangesAsync();
                return RedirectToAction(nameof(Dashboard));
            }
            return View(events);
        }

        // GET: Events/Edit/5
        public async Task<IActionResult> Edit(Guid? id)
        {
            if (id == null)
            {
                return NotFound();
            }

            var events = await _context.Events.FindAsync(id);
            if (events == null)
            {
                return NotFound();
            }
            return View(events);
        }

        // POST: Events/Edit/5
        // To protect from overposting attacks, enable the specific properties you want to bind to.
        // For more details, see http://go.microsoft.com/fwlink/?LinkId=317598.
        [HttpPost]
        [ValidateAntiForgeryToken]
        public async Task<IActionResult> Edit(Guid id, [Bind("Id,EventName,Description,Location,Date,Category,AttendeeCount")] Events events)
        {
            if (id != events.Id)
            {
                return NotFound();
            }

            if (ModelState.IsValid)
            {
                try
                {
                    _context.Update(events);
                    await _context.SaveChangesAsync();
                }
                catch (DbUpdateConcurrencyException)
                {
                    if (!EventsExists(events.Id))
                    {
                        return NotFound();
                    }
                    else
                    {
                        throw;
                    }
                }

                // Redirect to the Details page after saving changes
                return RedirectToAction("Details", new { id = events.Id });
            }
            return View(events);
        }

        // GET: Events/Delete/5
        public async Task<IActionResult> Delete(Guid? id)
        {
            if (id == null)
            {
                return NotFound();
            }

            var events = await _context.Events
                .FirstOrDefaultAsync(m => m.Id == id);
            if (events == null)
            {
                return NotFound();
            }

            return View(events);
        }

        // POST: Events/Delete/5
        [HttpPost, ActionName("Delete")]
        [ValidateAntiForgeryToken]
        public async Task<IActionResult> DeleteConfirmed(Guid id)
        {
            var events = await _context.Events.FindAsync(id);
            if (events != null)
            {
                _context.Events.Remove(events);
                await _context.SaveChangesAsync();
            }

            return RedirectToAction(nameof(Dashboard)); // Redirect to Dashboard or Index after deletion
        }


        private bool EventsExists(Guid id)
        {
            return _context.Events.Any(e => e.Id == id);
        }

        public IActionResult Dashboard()
        {
            // Fetch events from the database
            var events = _context.Events.ToList();

            // Pass the events to the view
            return View(events);
        }


        public async Task<IActionResult> MonthlyReport()
        {
            var report = await _context.Events
                .GroupBy(e => new { e.Date.Year, e.Date.Month })
                .Select(g => new MonthlyReportView
                {
                    Year = g.Key.Year,
                    Month = g.Key.Month,
                    EventCount = g.Count(),
                    TotalAttendees = g.Sum(e => Convert.ToInt64(e.AttendeeCount))
                })
                .OrderByDescending(r => r.Year).ThenByDescending(r => r.Month)
                .ToListAsync();

            return View(report);
        }

        public async Task<IActionResult> MonthlyDetails(int year, int month)
        {
            var events = await _context.Events
                .Where(e => e.Date.Year == year && e.Date.Month == month)
                .ToListAsync();

            return View(events);
        }

    }
}
