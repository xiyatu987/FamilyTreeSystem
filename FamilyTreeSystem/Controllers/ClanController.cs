using Microsoft.AspNetCore.Mvc;
using Microsoft.EntityFrameworkCore;
using FamilyTreeSystem.Data;
using FamilyTreeSystem.Models;
using System;
using System.Collections.Generic;
using System.Threading.Tasks;

namespace FamilyTreeSystem.Controllers
{
    public class ClanController : Controller
    {
        private readonly FamilyTreeContext _context;

        public ClanController(FamilyTreeContext context)
        {
            _context = context;
        }

        // GET: Clan
        public async Task<IActionResult> Index()
        {
            return View(await _context.Clans.ToListAsync());
        }

        // GET: Clan/Details/5
        public async Task<IActionResult> Details(int? id)
        {
            if (id == null)
            {
                return NotFound();
            }

            var clan = await _context.Clans
                .FirstOrDefaultAsync(m => m.Id == id);
            if (clan == null)
            {
                return NotFound();
            }

            return View(clan);
        }

        // GET: Clan/Create
        public IActionResult Create()
        {
            return View();
        }

        // POST: Clan/Create
        [HttpPost]
        [ValidateAntiForgeryToken]
        public async Task<IActionResult> Create([Bind("Id,Name,ClanSurname,HallName,AncestorName,AncestorBirthDate,OriginalAddress,Region,Description")] Clan clan)
        {
            if (ModelState.IsValid)
            {
                clan.UserId = 1; // 设置默认用户ID
                clan.CreatedAt = DateTime.Now;
                clan.UpdatedAt = DateTime.Now;
                _context.Add(clan);
                await _context.SaveChangesAsync();
                return RedirectToAction(nameof(Index));
            }
            return View(clan);
        }

        // GET: Clan/Edit/5
        public async Task<IActionResult> Edit(int? id)
        {
            if (id == null)
            {
                return NotFound();
            }

            var clan = await _context.Clans.FindAsync(id);
            if (clan == null)
            {
                return NotFound();
            }
            return View(clan);
        }

        // POST: Clan/Edit/5
        [HttpPost]
        [ValidateAntiForgeryToken]
        public async Task<IActionResult> Edit(int id, [Bind("Id,Name,ClanSurname,HallName,AncestorName,AncestorBirthDate,OriginalAddress,Region,Description")] Clan clan)
        {
            if (id != clan.Id)
            {
                return NotFound();
            }

            if (ModelState.IsValid)
            {
                try
                {
                    clan.UserId = 1; // 保持默认用户ID
                    clan.UpdatedAt = DateTime.Now;
                    _context.Update(clan);
                    await _context.SaveChangesAsync();
                }
                catch (DbUpdateConcurrencyException)
                {
                    if (!ClanExists(clan.Id))
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
            return View(clan);
        }

        // GET: Clan/Delete/5
        public async Task<IActionResult> Delete(int? id)
        {
            if (id == null)
            {
                return NotFound();
            }

            var clan = await _context.Clans
                .FirstOrDefaultAsync(m => m.Id == id);
            if (clan == null)
            {
                return NotFound();
            }

            return View(clan);
        }

        // POST: Clan/Delete/5
        [HttpPost, ActionName("Delete")]
        [ValidateAntiForgeryToken]
        public async Task<IActionResult> DeleteConfirmed(int id)
        {
            var clan = await _context.Clans.FindAsync(id);
            if (clan != null)
            {
                _context.Clans.Remove(clan);
                await _context.SaveChangesAsync();
            }
            return RedirectToAction(nameof(Index));
        }

        private bool ClanExists(int id)
        {
            return _context.Clans.Any(e => e.Id == id);
        }
    }
}