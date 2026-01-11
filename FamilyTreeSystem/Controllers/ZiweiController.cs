using Microsoft.AspNetCore.Mvc;
using Microsoft.EntityFrameworkCore;
using FamilyTreeSystem.Data;
using FamilyTreeSystem.Models;
using System.Collections.Generic;
using System.Threading.Tasks;

namespace FamilyTreeSystem.Controllers
{
    public class ZiweiController : Controller
    {
        private readonly FamilyTreeContext _context;

        public ZiweiController(FamilyTreeContext context)
        {
            _context = context;
        }

        // GET: Ziwei
        public async Task<IActionResult> Index()
        {
            // 按世代排序
            return View(await _context.Ziwei.OrderBy(z => z.Generation).ToListAsync());
        }

        // GET: Ziwei/Details/5
        public async Task<IActionResult> Details(int? ziweiId)
        {
            if (ziweiId == null)
            {
                return NotFound();
            }

            var ziwei = await _context.Ziwei
                .FirstOrDefaultAsync(m => m.ZiweiId == ziweiId);
            if (ziwei == null)
            {
                return NotFound();
            }

            return View(ziwei);
        }

        // GET: Ziwei/Create
        public IActionResult Create()
        {
            return View();
        }

        // POST: Ziwei/Create
        [HttpPost]
        [ValidateAntiForgeryToken]
        public async Task<IActionResult> Create([Bind("ZiweiId,ZiweiChar,Description")] Ziwei ziwei)
        {
            if (ModelState.IsValid)
            {
                // 计算新的世代值（当前最大世代+1）
                var maxGeneration = await _context.Ziwei.MaxAsync(z => (int?)z.Generation) ?? 0;
                ziwei.Generation = maxGeneration + 1;
                
                ziwei.UserId = 1; // 设置默认用户ID
                _context.Add(ziwei);
                await _context.SaveChangesAsync();
                return RedirectToAction(nameof(Index));
            }
            return View(ziwei);
        }

        // GET: Ziwei/Edit/5
        public async Task<IActionResult> Edit(int? ziweiId)
        {
            if (ziweiId == null)
            {
                return NotFound();
            }

            var ziwei = await _context.Ziwei.FindAsync(ziweiId);
            if (ziwei == null)
            {
                return NotFound();
            }
            return View(ziwei);
        }

        // POST: Ziwei/Edit/5
        [HttpPost]
        [ValidateAntiForgeryToken]
        public async Task<IActionResult> Edit(int ziweiId, [Bind("ZiweiId,ZiweiChar,Generation,Description")] Ziwei ziwei)
        {
            if (ziweiId != ziwei.ZiweiId)
            {
                return NotFound();
            }

            if (ModelState.IsValid)
            {
                try
                {
                    ziwei.UserId = 1; // 保持默认用户ID
                    _context.Update(ziwei);
                    await _context.SaveChangesAsync();
                }
                catch (DbUpdateConcurrencyException)
                {
                    if (!ZiweiExists(ziwei.ZiweiId))
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
            return View(ziwei);
        }

        // GET: Ziwei/Delete/5
        public async Task<IActionResult> Delete(int? ziweiId)
        {
            if (ziweiId == null)
            {
                return NotFound();
            }

            var ziwei = await _context.Ziwei
                .FirstOrDefaultAsync(m => m.ZiweiId == ziweiId);
            if (ziwei == null)
            {
                return NotFound();
            }

            return View(ziwei);
        }

        // POST: Ziwei/Delete/5
        [HttpPost, ActionName("Delete")]
        [ValidateAntiForgeryToken]
        public async Task<IActionResult> DeleteConfirmed(int ziweiId)
        {
            var ziwei = await _context.Ziwei.FindAsync(ziweiId);
            if (ziwei != null)
            {
                _context.Ziwei.Remove(ziwei);
            }
            await _context.SaveChangesAsync();
            return RedirectToAction(nameof(Index));
        }

        private bool ZiweiExists(int ziweiId)
        {
            return _context.Ziwei.Any(e => e.ZiweiId == ziweiId);
        }
    }
}