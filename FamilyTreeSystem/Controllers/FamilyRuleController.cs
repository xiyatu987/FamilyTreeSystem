using Microsoft.AspNetCore.Mvc;
using Microsoft.EntityFrameworkCore;
using FamilyTreeSystem.Data;
using FamilyTreeSystem.Models;
using System.Collections.Generic;
using System.Threading.Tasks;

namespace FamilyTreeSystem.Controllers
{
    public class FamilyRuleController : Controller
    {
        private readonly FamilyTreeContext _context;

        public FamilyRuleController(FamilyTreeContext context)
        {
            _context = context;
        }

        // 用于更新祖训内容的模型
        public class UpdateFamilyRuleContentModel
        {
            public int Id { get; set; }
            public string Content { get; set; } = string.Empty;
        }

        // GET: FamilyRule
        public async Task<IActionResult> Index()
        {
            return View(await _context.FamilyRules.ToListAsync());
        }

        // GET: FamilyRule/Details/5
        public async Task<IActionResult> Details(int? id)
        {
            if (id == null)
            {
                return NotFound();
            }

            var familyRule = await _context.FamilyRules
                .FirstOrDefaultAsync(m => m.Id == id);
            if (familyRule == null)
            {
                return NotFound();
            }

            return View(familyRule);
        }

        // GET: FamilyRule/Create
        public IActionResult Create()
        {
            return View();
        }

        // POST: FamilyRule/Create
        [HttpPost]
        [ValidateAntiForgeryToken]
        public async Task<IActionResult> Create([Bind("Id,Title,Content,CreatedDate,Creator")] FamilyRule familyRule)
        {
            if (ModelState.IsValid)
            {
                familyRule.UserId = 1; // 设置默认用户ID
                // 如果没有提供创建日期，默认设置为当天
                if (familyRule.CreatedDate == null)
                {
                    familyRule.CreatedDate = DateTime.Now;
                }
                _context.Add(familyRule);
                await _context.SaveChangesAsync();
                return RedirectToAction(nameof(Index));
            }
            return View(familyRule);
        }

        // GET: FamilyRule/Edit/5
        public async Task<IActionResult> Edit(int? id)
        {
            if (id == null)
            {
                return NotFound();
            }

            var familyRule = await _context.FamilyRules.FindAsync(id);
            if (familyRule == null)
            {
                return NotFound();
            }
            return View(familyRule);
        }

        // POST: FamilyRule/Edit/5
        [HttpPost]
        [ValidateAntiForgeryToken]
        public async Task<IActionResult> Edit(int id, [Bind("Id,Title,Content,CreatedDate,Creator")] FamilyRule familyRule)
        {
            if (id != familyRule.Id)
            {
                return NotFound();
            }

            if (ModelState.IsValid)
            {
                try
                {
                    familyRule.UserId = 1; // 保持默认用户ID
                    _context.Update(familyRule);
                    await _context.SaveChangesAsync();
                }
                catch (DbUpdateConcurrencyException)
                {
                    if (!FamilyRuleExists(familyRule.Id))
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
            return View(familyRule);
        }

        // POST: FamilyRule/UpdateContent
        [HttpPost]
        [ValidateAntiForgeryToken]
        public async Task<IActionResult> UpdateContent([FromBody] UpdateFamilyRuleContentModel model)
        {
            if (ModelState.IsValid)
            {
                var familyRule = await _context.FamilyRules.FindAsync(model.Id);
                if (familyRule == null)
                {
                    return NotFound();
                }

                familyRule.Content = model.Content;
                familyRule.UpdatedAt = System.DateTime.Now;

                try
                {
                    await _context.SaveChangesAsync();
                    return Ok();
                }
                catch (DbUpdateConcurrencyException)
                {
                    if (!FamilyRuleExists(model.Id))
                    {
                        return NotFound();
                    }
                    else
                    {
                        throw;
                    }
                }
            }
            return BadRequest(ModelState);
        }

        // GET: FamilyRule/Delete/5
        public async Task<IActionResult> Delete(int? id)
        {
            if (id == null)
            {
                return NotFound();
            }

            var familyRule = await _context.FamilyRules
                .FirstOrDefaultAsync(m => m.Id == id);
            if (familyRule == null)
            {
                return NotFound();
            }

            return View(familyRule);
        }

        // POST: FamilyRule/Delete/5
        [HttpPost, ActionName("Delete")]
        [ValidateAntiForgeryToken]
        public async Task<IActionResult> DeleteConfirmed(int id)
        {
            var familyRule = await _context.FamilyRules.FindAsync(id);
            if (familyRule != null)
            {
                _context.FamilyRules.Remove(familyRule);
                await _context.SaveChangesAsync();
            }
            return RedirectToAction(nameof(Index));
        }

        private bool FamilyRuleExists(int id)
        {
            return _context.FamilyRules.Any(e => e.Id == id);
        }
    }
}