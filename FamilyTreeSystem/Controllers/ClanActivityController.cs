using Microsoft.AspNetCore.Mvc;
using Microsoft.EntityFrameworkCore;
using FamilyTreeSystem.Data;
using FamilyTreeSystem.Models;
using System.Collections.Generic;
using System.Threading.Tasks;

namespace FamilyTreeSystem.Controllers
{
    public class ClanActivityController : Controller
    {
        private readonly FamilyTreeContext _context;

        public ClanActivityController(FamilyTreeContext context)
        {
            _context = context;
        }

        // GET: ClanActivity
        public async Task<IActionResult> Index()
        {
            return View(await _context.ClanActivities.ToListAsync());
        }

        // GET: ClanActivity/Details/5
        public async Task<IActionResult> Details(int? id)
        {
            if (id == null)
            {
                return NotFound();
            }

            var clanActivity = await _context.ClanActivities
                .FirstOrDefaultAsync(m => m.Id == id);
            if (clanActivity == null)
            {
                return NotFound();
            }

            return View(clanActivity);
        }

        // GET: ClanActivity/Create
        public IActionResult Create()
        {
            return View();
        }

        // POST: ClanActivity/Create
        [HttpPost]
        [ValidateAntiForgeryToken]
        public async Task<IActionResult> Create([Bind("Id,Title,Description,Location,ActivityDate,Category")] ClanActivity clanActivity)
        {
            if (ModelState.IsValid)
            {
                clanActivity.UserId = 1; // 设置默认用户ID
                _context.Add(clanActivity);
                await _context.SaveChangesAsync();
                return RedirectToAction(nameof(Index));
            }
            return View(clanActivity);
        }

        // GET: ClanActivity/Edit/5
        public async Task<IActionResult> Edit(int? id)
        {
            if (id == null)
            {
                return NotFound();
            }

            var clanActivity = await _context.ClanActivities.FindAsync(id);
            if (clanActivity == null)
            {
                return NotFound();
            }
            return View(clanActivity);
        }

        // POST: ClanActivity/Edit/5
        [HttpPost]
        [ValidateAntiForgeryToken]
        public async Task<IActionResult> Edit(int id, [Bind("Id,Title,Description,Location,ActivityDate,Category")] ClanActivity clanActivity)
        {
            if (id != clanActivity.Id)
            {
                return NotFound();
            }

            if (ModelState.IsValid)
            {
                try
                {
                    clanActivity.UserId = 1; // 保持默认用户ID
                    _context.Update(clanActivity);
                    await _context.SaveChangesAsync();
                }
                catch (DbUpdateConcurrencyException)
                {
                    if (!ClanActivityExists(clanActivity.Id))
                    {
                        return NotFound();
                    }
                    else
                    {
                        throw;
                    }
                }
                return RedirectToAction(nameof(Index));
            }
            return View(clanActivity);
        }

        // GET: ClanActivity/Delete/5
        public async Task<IActionResult> Delete(int? id)
        {
            if (id == null)
            {
                return NotFound();
            }

            var clanActivity = await _context.ClanActivities
                .FirstOrDefaultAsync(m => m.Id == id);
            if (clanActivity == null)
            {
                return NotFound();
            }

            return View(clanActivity);
        }

        // POST: ClanActivity/Delete/5
        [HttpPost, ActionName("Delete")]
        [ValidateAntiForgeryToken]
        public async Task<IActionResult> DeleteConfirmed(int id)
        {
            var clanActivity = await _context.ClanActivities.FindAsync(id);
            if (clanActivity != null)
            {
                _context.ClanActivities.Remove(clanActivity);
                await _context.SaveChangesAsync();
            }
            return RedirectToAction(nameof(Index));
        }

        private bool ClanActivityExists(int id)
        {
            return _context.ClanActivities.Any(e => e.Id == id);
        }
    }
}